<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Observer;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Framework\Event\Observer;
use Magento\Paypal\Model\Cart as PayPalCart;

class RemoveStoreCreditFromPayment implements \Magento\Framework\Event\ObserverInterface
{
    public const SALES_METHOD_ATTRIBUTE = 'entity_type';
    public const ORDER_SALES_METHOD = 'order';
    public const INVOICE_SALES_METHOD = 'invoice';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->configProvider->isEnabled()) {
            $cart = $observer->getData('cart');
            $salesEntity = $cart->getSalesModel();
            $usingSalesMethod = $salesEntity->getDataUsingMethod(self::SALES_METHOD_ATTRIBUTE);

            if ($usingSalesMethod === self::ORDER_SALES_METHOD
                || $salesEntity->getDataUsingMethod(SalesFieldInterface::AMSC_USE)
                || ($cart instanceof PayPalCart && $usingSalesMethod === self::INVOICE_SALES_METHOD)
            ) {
                $value = abs((float)$salesEntity->getDataUsingMethod(SalesFieldInterface::AMSC_BASE_AMOUNT));
                if ($value > 0.0001) {
                    $cart->addDiscount((double)$value);
                }
            }
        }
    }
}
