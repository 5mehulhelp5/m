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
namespace Webkul\QuickPayForOrder\Plugin\Block\Checkout;

use Webkul\QuickPayForOrder\Model\QuickPayConfigProvider;

class Billing
{
    protected $quickPayConfigProvider;
    
    /**
     * @param QuickPayConfigProvider $redirectFactory
     */
    public function __construct(
        QuickPayConfigProvider $quickPayConfigProvider
    ) {
        $this->quickPayConfigProvider = $quickPayConfigProvider;
    }

    /**
     * @see \Magento\Multishipping\Block\Checkout\Billing::getMethods()
     */
    public function afterGetMethods(\Magento\Multishipping\Block\Checkout\Billing $subject, $result)
    {
        $quickPayQuoteId = null;
        $config = $this->quickPayConfigProvider->getConfig();
        $currentQuoteId = $subject->getQuote()->getId();
        if (empty($config)) {
            return $result;
        }

        if (isset($config['quickPayQuoteId'])) {
            $quickPayQuoteId = $config['quickPayQuoteId'];
        }

        if ($quickPayQuoteId != $currentQuoteId) {
            return $result;
        }

        if ($quickPayQuoteId && !isset($config['allowAllPaymentMethods'])
            && $config['allowAllPaymentMethods'] == 1
        ) {
            return $result;
        }

        if ($quickPayQuoteId && isset($config['allowAllPaymentMethods'])
            && $config['allowAllPaymentMethods'] == 0
            && !empty($config['quickPayPaymentMethods'])
        ) {
            $quickMethods = $config['quickPayPaymentMethods'];
            if (empty($result) || empty($quickMethods)) {
                return $result;
            }
            $this->checkAvailablity($quickMethods, $result);
        }

        return $result;
    }

    /**
     * @param array $quickMethods
     * @param array $result
     */
    public function checkAvailablity($quickMethods, &$result)
    {
        foreach ($result as $key => $_method) {
            if (!in_array($_method->getCode(), $quickMethods)) {
                unset($result[$key]);
            }
        }
    }
}
