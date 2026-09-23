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

class Shipping
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
     * @see \Magento\Multishipping\Block\Checkout\Shipping::getShippingRates()
     */
    public function afterGetShippingRates(\Magento\Multishipping\Block\Checkout\Shipping $subject, $result)
    {
        $quickPayQuoteId = null;
        $config = $this->quickPayConfigProvider->getConfig();
        $currentQuoteId = $subject->getCheckout()->getQuote()->getId();
        if (empty($config)) {
            return $result;
        }

        if (isset($config['quickPayQuoteId'])) {
            $quickPayQuoteId = $config['quickPayQuoteId'];
        }

        if ($quickPayQuoteId != $currentQuoteId) {
            return $result;
        }

        if ($quickPayQuoteId && !isset($config['allowAllShippingMethods'])
            && $config['allowAllShippingMethods'] == 1
        ) {
            return $result;
        }

        if ($quickPayQuoteId && isset($config['allowAllShippingMethods'])
            && $config['allowAllShippingMethods'] == 0
            && !empty($config['quickPayShippingMethod'])
        ) {
            $quickMethods = explode(',', $config['quickPayShippingMethod']);
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
        foreach ($result as $code => $_rates) {
            foreach ($_rates as $key => $_rate) {
                if (!in_array($_rate->getCode(), $quickMethods)) {
                    if (count($_rates) == 1) {
                        unset($result[$code]);
                    } else {
                        unset($result[$code][$key]);
                    }
                }
            }
        }
    }
}
