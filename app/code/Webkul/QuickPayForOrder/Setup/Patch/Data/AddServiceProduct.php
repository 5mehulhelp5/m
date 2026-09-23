<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\QuickPayForOrder\Setup\Patch\Data;

use Magento\Framework\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class to add service product
 */
class AddServiceProduct implements DataPatchInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $_catalogProductTypeManager;
    
    protected $sourceCollection;
    protected $sourceItemsProcessor;
    protected $productRepository;

    /**
     * @param \Magento\Catalog\Model\Product $productModel
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\State $appstate
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $catalogProductTypeManager
     * @param \Magento\Inventory\Model\ResourceModel\Source\Collection $sourceCollection
     * @param \Magento\InventoryCatalog\Model\SourceItemsProcessor $sourceItemsProcessor
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\Product $productModel,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\State $appstate,
        \Magento\Catalog\Model\Product\TypeTransitionManager $catalogProductTypeManager,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $sourceCollection = null,
        \Magento\InventoryCatalog\Model\SourceItemsProcessor $sourceItemsProcessor = null,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->_productModel = $productModel;
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->_eavConfig = $eavConfig;
        $this->_appState = $appstate;
        $this->_catalogProductTypeManager = $catalogProductTypeManager;
        $this->sourceCollection = $sourceCollection;
        $this->sourceItemsProcessor = $sourceItemsProcessor;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        if (!$this->_productModel->getIdBySku('wk_service')) {
            $this->_eavConfig->clear();
            try {
                $this->_appState->getAreaCode();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
            }
            $attributeSetId = $this->_productModel->getDefaultAttributeSetId();
            $mageProduct = $this->_productFactory->create();
            $mageProduct->setAttributeSetId($attributeSetId);
            $mageProduct->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL);
            $mageProduct->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $requestData = [
                'product' => [
                    'name' => 'Service',
                    'attribute_set_id' => $attributeSetId,
                    'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                    'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE,
                    'sku' => 'wk_service',
                    'tax_class_id' => 0,
                    'description' => 'Service product',
                    'short_description' => 'Service product',
                    'stock_data' => [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 0,
                        'is_decimal_divided' => 0
                    ],
                    'quantity_and_stock_status' => [
                        'qty' => 1,
                        'is_in_stock' => 1
                    ]
                ]
            ];
            $catalogProduct = $this->productInitialize($mageProduct, $requestData);
            $this->_catalogProductTypeManager->processProduct($catalogProduct);
            $catalogProduct->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)->save();
            $catalogProduct->save();
            if ($this->sourceCollection && $this->sourceItemsProcessor) {
                $this->updateSourceForProduct('wk_service');
            }
        }
    }

    /**
     * Set quantity 1 for every source
     *
     * @param string $sku
     * @return null
     */
    private function updateSourceForProduct($sku)
    {
        $sourceListArr = $this->sourceCollection->load();
        $sourceData = [];
        foreach ($sourceListArr as $sourceItem) {
            $sourceData[] = [
                'source_code' => $sourceItem->getSourceCode(),
                'status' => 1,
                'quantity' => 1
            ];
        }
        if (!empty($sourceData)) {
            $this->sourceItemsProcessor->execute(
                $sku,
                $sourceData
            );
        }
    }

    /**
     * initialize product data
     *
     * @param \Magento\Catalog\Model\Product $catalogProduct
     * @param array $requestData
     * @return \Magento\Catalog\Model\Product
     */
    private function productInitialize(\Magento\Catalog\Model\Product $catalogProduct, $requestData)
    {
        $requestProductData = $requestData['product'];
        $requestProductData['product_has_weight'] = 0;
        $catalogProduct->addData($requestProductData);
        $websiteIds = [];
        $allWebsites = $this->_storeManager->getWebsites();
        foreach ($allWebsites as $website) {
            $websiteIds[] = $website->getId();
        }
        $catalogProduct->setWebsiteIds($websiteIds);
        return $catalogProduct;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
