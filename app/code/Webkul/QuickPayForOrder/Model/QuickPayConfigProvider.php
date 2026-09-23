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

namespace Webkul\QuickPayForOrder\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;
use Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory;
use Webkul\QuickPayForOrder\Helper\Data;

class QuickPayConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var QuickPayOrdersFactory
     */
    protected $quickPayOrdersFactory;
    
    protected $helper;

    /**
     * @param Session $customerSession
     * @param QuickPayOrdersFactory $quickPayOrdersFactory
     */
    public function __construct(
        Session $customerSession,
        QuickPayOrdersFactory $quickPayOrdersFactory,
        Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];

        if ($this->customerSession->getQuickPayId()) {
            $quickPayData = $this->quickPayOrdersFactory->create()->load($this->customerSession->getQuickPayId());
            $quote = $this->helper->getQuoteById($quickPayData->getQuoteId());
            $validationFlag = $this->helper->validateQuote($quote);
            $config = [
                'quickPayPaymentMethods' => explode(',', $quickPayData->getPaymentMethods()),
                'quickPayQuoteId' => $quickPayData->getQuoteId(),
                'quickPayShippingMethod' => $quickPayData->getShippingMethod(),
                'allowAllPaymentMethods' => $quickPayData->getAllPaymentMethods(),
                'allowAllShippingMethods' => $quickPayData->getAllShippingMethods(),
                'quickPayQuoteCurrency' => $quickPayData->getQuoteCurrencyCode(),
                'quickPayValidationFlag' => $validationFlag
            ];
        }
        
        return $config;
    }
}
