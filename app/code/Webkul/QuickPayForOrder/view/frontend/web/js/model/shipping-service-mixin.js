/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    'mage/utils/wrapper',
    'underscore'
], function (wrapper, _) {
    'use strict';

    return function (targetModule) {
        var originalSetShippingRates = targetModule.setShippingRates;
        var customSetShippingRates = wrapper.wrap(originalSetShippingRates, function (original) {
            if (window.checkoutConfig.allowAllShippingMethods == 1) {
                original();
            } else {
                let currentQuoteId = window.checkoutConfig.quoteData.entity_id;
                let quickPayQuoteId = window.checkoutConfig.quickPayQuoteId;
                let quickPayShippingMethod = window.checkoutConfig.quickPayShippingMethod;
                let args = _.toArray(arguments);
                let superArgs = args[1];
                let shippingMethod = [];
                
                if (quickPayShippingMethod && superArgs.length && (currentQuoteId == quickPayQuoteId)) {
                    superArgs.forEach(function (method) {
                        if (method['carrier_code'] + '_' + method['method_code'] === quickPayShippingMethod) {
                            shippingMethod.push(method);
                        }
                    });
                }

                (shippingMethod.length) ? original(shippingMethod) : original();
            }
        });

        targetModule.setShippingRates = customSetShippingRates;
        return targetModule;
    };
});
