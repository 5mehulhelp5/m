<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\Catalog;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CategoriesPathResolver
{
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Root categories map storage
     *
     * @var array
     */
    private $rootCategoriesMap;

    /**
     * Categories map storage
     *
     * @var array
     */
    private $categoriesMap;

    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->initCategoriesPerStore();
    }

    /**
     * Get category name path by entity_id
     *
     * @param int $entityId
     * @param int $storeId
     * @return string
     */
    public function getNamePathByEntityId(int $entityId, int $storeId): string
    {
        return trim(($this->rootCategoriesMap[$storeId][$entityId] ?? '')
            . '/' . ($this->categoriesMap[$storeId][$entityId] ?? ''), '/');
    }

    /**
     * Prepare categories mapping for each store view
     */
    private function initCategoriesPerStore(): void
    {
        foreach ($this->storeManager->getStores(true) as $store) {
            $storeId = $store->getId();
            $collection = $this->categoryCollectionFactory->create()
                ->addNameToResult()
                ->setStoreId($storeId);

            foreach ($collection as $category) {
                $structure = preg_split('#/+#', $category->getPath());
                $pathSize = count($structure);
                if ($pathSize > 1) {
                    $path = [];
                    for ($i = 1; $i < $pathSize; $i++) {
                        $childCategory = $collection->getItemById($structure[$i]);
                        if ($childCategory) {
                            $name = $childCategory->getName();
                            $path[] = str_replace(
                                CategoryProcessor::DELIMITER_CATEGORY,
                                '\\' . CategoryProcessor::DELIMITER_CATEGORY,
                                $name
                            );
                        }
                    }
                    $this->rootCategoriesMap[$storeId][$category->getId()] = array_shift($path);
                    if ($pathSize > 2) {
                        $this->categoriesMap[$storeId][$category->getId()] = implode(
                            CategoryProcessor::DELIMITER_CATEGORY,
                            $path
                        );
                    }
                }
            }
        }
    }
}
