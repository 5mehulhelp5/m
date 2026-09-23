<?php

namespace Crimson\ProductBackorderNotification\Block;

use Crimson\ProductBackorderNotification\Helper\BackorderNotification as BackorderNotificationHelper;
use Crimson\ProductBackorderNotification\Helper\Data as DataHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class BackorderInformation extends Template
{
    const TYPE_CODE = 'configurable';

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var BackorderNotificationHelper
     */
    protected $backorderNotificationHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * BackorderInformation constructor.
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @param BackorderNotificationHelper $backorderNotificationHelper
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $dataHelper,
        BackorderNotificationHelper $backorderNotificationHelper,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->dataHelper = $dataHelper;
        $this->backorderNotificationHelper = $backorderNotificationHelper;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $this->_product = $product;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDisplayBackorderInformation()
    {
        $product = $this->getProduct();
        return $this->backorderNotificationHelper->getIsDisplayBackorderInformation($product);
    }

    /**
     * @return bool
     */
    public function getBackorderMessage()
    {
        $product = $this->getProduct();
        return $this->backorderNotificationHelper->getBackorderMessage($product);
    }

    /**
     * @return false|string
     */
    public function getStockJson()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getProduct();
        if ($product->getTypeId() != self::TYPE_CODE) {
            return '{}';
        }

        $config = [];
        $_children = $product->getTypeInstance()->getUsedProducts($product);
        foreach($_children as $_simpleProduct) {
            $message = '';
            $simple = $this->productRepository->getById($_simpleProduct->getId());
            if ($this->backorderNotificationHelper->getIsDisplayBackorderInformation($simple)) {
                $message = $this->backorderNotificationHelper->getBackorderMessage($simple);
            }
            $config[$simple->getId()] = $message;
        }

        return json_encode($config);
    }
}
