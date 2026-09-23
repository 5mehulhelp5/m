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

namespace Webkul\QuickPayForOrder\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Backend\Model\Session;

class AddOptions extends Action
{
    protected $_resultJsonFactory;
    protected $_productFactory;
    protected $_productModel;
    protected $_storeManager;
    protected $_catalogProductTypeManager;
    protected $zendUri;
    
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ProductFactory $productFactory
     * @param Session $session
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $catalogProductTypeManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductFactory $productFactory,
        Session $session,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\TypeTransitionManager $catalogProductTypeManager,
        \Laminas\Uri\Uri $zendUri
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_productFactory = $productFactory;
        $this->_session = $session;
        $this->_productModel = $productModel;
        $this->_storeManager = $storeManager;
        $this->_catalogProductTypeManager = $catalogProductTypeManager;
        $this->zendUri = $zendUri;
        parent::__construct($context);
    }

    /**
     * Set additional options in service product
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $formData = $additionalOptions = $response = [];
        $productId = '';
        $data = $this->getRequest()->getParams();
        try {
            if ($data['formData'] != '') {
                $this->zendUri->setQuery($data['formData']);
                $formData = $this->zendUri->getQueryAsArray();
            }
            if (isset($data['productId']) && $data['productId'] != '') {
                $productId = $data['productId'];
            } else {
                $productId = $this->createNew();
            }

            if ($productId) {
                $additionalOptions['productId'] = $productId;
                $product = $this->_productFactory->create()->load($additionalOptions['productId']);
                if (!empty($formData)) {
                    foreach ($formData['product'] as $values) {
                        $additionalOptions['options'][] = [
                            'label' => $values['name'],
                            'value' => $values['price']
                        ];
                    }
                }
                $this->_session->setAdditionalOptions(json_encode($additionalOptions));
                $response = ['status' => 'ok', 'message' => 'success', 'productId' => $productId];
            }
        } catch (\Exception $e) {
            $response = ['status' => 'fail', 'message' => $e->getMessage()];
        }
        $result = $this->_resultJsonFactory->create();
        return $result->setData($response);
    }

    /**
     * create new service product
     *
     * @return int
     */
    private function createNew()
    {
        $attributeSetId = $this->_productModel->getDefaultAttributeSetId();
        $store = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        $mageProduct = $this->_productFactory->create();
        $mageProduct->setAttributeSetId($attributeSetId);
        $mageProduct->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL);
        $mageProduct->setStoreId($store);
        $mageProduct->setPrice(0);

        $requestData = [
            'product' => [
                'name' => 'Service',
                'sku' => 'wk_service',
                'attribute_set_id' => $attributeSetId,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE,
                'short_description' => 'Service product',
                'tax_class_id' => 0,
                'description' => 'Service product',
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

        return $catalogProduct->getId();
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
}
