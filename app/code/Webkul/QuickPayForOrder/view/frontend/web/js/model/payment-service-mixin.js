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
        var originalSetPaymentMethods = targetModule.setPaymentMethods;
        var customSetPaymentMethods = wrapper.wrap(originalSetPaymentMethods, function (original) {
            if (window.checkoutConfig.allowAllPaymentMethods == 1) {
                original();
            } else {
                let currentQuoteId = window.checkoutConfig.quoteData.entity_id;
                let quickPayQuoteId = window.checkoutConfig.quickPayQuoteId;
                let quickPayPaymentMethods = window.checkoutConfig.quickPayPaymentMethods;
                let args = _.toArray(arguments);
                let superArgs = args[1];
                let paymentMethods = [];
                if (quickPayPaymentMethods) {
                    if (quickPayPaymentMethods.length && superArgs.length && (currentQuoteId == quickPayQuoteId)) {
                        superArgs.forEach(function (method) {
                            if (quickPayPaymentMethods.indexOf(method.method) != -1) {
                                paymentMethods.push(method);
                            }
                        });
                    }
                }

                (paymentMethods.length) ? original(paymentMethods) : original();
            }
        });

        targetModule.setPaymentMethods = customSetPaymentMethods;
        return targetModule;
    };
});
