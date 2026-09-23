<?php

namespace CyberSolutionsLLC\OlegKovalRegenerateUrlRewrites\Model;

/**
 *
 */
class RegenerateProductRewrites extends \OlegKoval\RegenerateUrlRewrites\Model\RegenerateProductRewrites {
    
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableType;

    /**
     * @param \OlegKoval\RegenerateUrlRewrites\Helper\Regenerate $helper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productActionFactory
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGeneratorFactory\Proxy $productUrlRewriteGeneratorFactory
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGeneratorFactory\Proxy $productUrlPathGeneratorFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     */
    public function __construct(
        \OlegKoval\RegenerateUrlRewrites\Helper\Regenerate $helper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productActionFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGeneratorFactory\Proxy $productUrlRewriteGeneratorFactory,
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGeneratorFactory\Proxy $productUrlPathGeneratorFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
    ) {
        $this->configurableType = $configurableType;
        parent::__construct(
            $helper,
            $resourceConnection,
            $productActionFactory,
            $productUrlRewriteGeneratorFactory,
            $productUrlPathGeneratorFactory,
            $productCollectionFactory
        );
    }

    /**
     * Get products collection
     *
     * @param array $productsFilter
     * @param int $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductsCollection(array $productsFilter = [], int $storeId = 0): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        $productsCollection = $this->productCollectionFactory->create();

        $productsCollection->setStore($storeId)
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path')
            /*
             * adding rewrites for non-visible simple products 
            ->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE])
            */
            // use limit to avoid an "eating" of a memory
            ->setPageSize($this->productsCollectionPageSize);

        if (count($productsFilter) > 0) {
            $productsCollection->addIdFilter($productsFilter);
        }

        return $productsCollection;
    }
    
    /**
     * @param $entity
     * @param int $storeId
     * @return $this
     */
    public function processProduct($entity, int $storeId = 0): static {
        $entity->setStoreId($storeId)->setData('url_path', null);

        if ($this->regenerateOptions['saveOldUrls']) {
            $entity->setData('save_rewrites_history', true);
        }

        // reset url_path to null, we need this to set a flag to use an Url Rewrites:
        // see logic in a core Product Url model: \Magento\Catalog\Model\Product\Url::getUrl()
        // if "request_path" is not null or equal to "false" then Magento do not search and do not use Url Rewrites
        $updateAttributes = ['url_path' => null];
        if (!$this->regenerateOptions['noRegenUrlKey']) {
            $generatedKey = $this->_getProductUrlPathGenerator()->getUrlKey($entity->setUrlKey(null));
            $updateAttributes['url_key'] = $generatedKey;
        }

        try {
            $this->_getProductAction()->updateAttributes(
                [$entity->getId()],
                $updateAttributes,
                $storeId
            );

            /* Add rewrite for non-visible configurable children */
            $entityVisibility = $entity->getVisibility();
            if (($entity->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
                && ($entityVisibility == \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE)
                && count($this->configurableType->getParentIdsByChild($entity->getId()))
            ) {
                $entity->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG);
            }
            $urlRewrites = $this->_getProductUrlRewriteGenerator()->generate($entity);
            $entity->setVisibility($entityVisibility);
            $urlRewrites = $this->helper->sanitizeProductUrlRewrites($urlRewrites);

            if (!empty($urlRewrites)) {
                $this->saveUrlRewrites(
                    $urlRewrites,
                    [['entity_type' => $this->entityType, 'entity_id' => $entity->getId(), 'store_id' => $storeId]]
                );
            }
        } catch (\Exception $e) {
            // go to the next product
        }

        $this->progressBarProgress++;

        return $this;
    }
    
}
