/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

var config = {
    'config':{
        'mixins': {
            'Magento_Checkout/js/model/payment-service': {
                'Webkul_QuickPayForOrder/js/model/payment-service-mixin':true
            },
            'Magento_Checkout/js/model/shipping-service': {
                'Webkul_QuickPayForOrder/js/model/shipping-service-mixin': true
            }
        }
    }
};