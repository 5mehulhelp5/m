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
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Webkul_QuickPayForOrder/js/model/validator'
    ], function (Component, additionalValidators, validator) {
        'use strict';
        additionalValidators.registerValidator(validator);
        return Component.extend({});
    }
);
