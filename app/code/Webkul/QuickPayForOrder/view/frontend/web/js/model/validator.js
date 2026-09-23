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
    'mage/translate',
    'Magento_Ui/js/model/messageList'
], function ($t, messageList) {
    'use strict';
    return {
        validate: function () {
            let isValid = true;
            let currentQuoteId = window.checkoutConfig.quoteData.entity_id;
            let quickPayQuoteId = window.checkoutConfig.quickPayQuoteId;

            if (window.checkoutConfig.quickPayQuoteCurrency
                && window.checkoutConfig.quickPayQuoteCurrency != window.checkoutConfig.quoteData.quote_currency_code
                && currentQuoteId == quickPayQuoteId
                && window.checkoutConfig.quickPayValidationFlag == true
            ) {
                isValid = false;
                messageList.addErrorMessage({
                    message: $t(
                        "Switch currency to %1 to complete this order"
                    ).replace('%1', window.checkoutConfig.quickPayQuoteCurrency)
                });
            }

            return isValid;
        }
    }
});
