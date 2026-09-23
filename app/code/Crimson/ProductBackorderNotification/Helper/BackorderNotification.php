<?php

namespace Crimson\ProductBackorderNotification\Helper;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class BackorderNotification extends AbstractHelper
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku = null;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * BackorderNotification constructor.
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param Data $dataHelper
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        Data $dataHelper,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->stockRegistry = $stockRegistry;
        $this->dataHelper = $dataHelper;
        $this->_objectManager = $objectManager;
        if ($this->checkMsiModulesEnabled()) {
            $this->getSalableQuantityDataBySku = $this->_objectManager->get(\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku::class);
        }
    }

    /**
     * @return int
     */
    public function getBackorderStatus($product)
    {
        $backorderStatus = 0;
        try {
            if ($product->getExtensionAttributes()->getStockItem()) {
                $backorderStatus = $product->getExtensionAttributes()->getStockItem()->getBackorders();
            } else {
                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                $backorderStatus = $stockItem->getBackorders();
            }
        } catch (\Exception $exception) {
        }

        return $backorderStatus;
    }

    /**
     * @param $product
     * @return bool
     */
    public function getIsDisplayBackorderInformation($product)
    {
        $backorderStatus = $this->getBackorderStatus($product);
        if ($this->getSalableQuantityDataBySku) {
            $salableQty = $this->getSalableQuantityDataBySku->execute($product->getData('sku'));
        } else {
            $stockItem = $this->stockRegistry->getStockItem($product->getId());
            $salableQty = $stockItem->getQty();
        }

        return $backorderStatus != 0 && $salableQty < 1;
    }

    /**
     * @param $product
     * @return bool
     */
    public function getBackorderMessage($product)
    {
        $defaultMessage = $this->dataHelper->getDefaultBackorderMessage();

        return $product->getData('backorder_notification') ?? $defaultMessage;
    }

    public function checkMsiModulesEnabled()
    {
        if ($this->_moduleManager->isEnabled('Magento_InventoryApi')
            && $this->_moduleManager->isEnabled('Magento_InventorySalesApi')
            && $this->_moduleManager->isEnabled('Magento_InventorySalesAdminUi')
            && $this->_moduleManager->isEnabled('Magento_InventoryCatalogAdminUi')
            && $this->_moduleManager->isEnabled('Magento_InventoryReservationsApi')
        ) {
            return true;
        } else {
            return false;
        }
    }
}
