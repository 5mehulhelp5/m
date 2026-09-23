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
    map: {
        '*': {
            'Magento_Sales/order/create/scripts':'Webkul_QuickPayForOrder/order/create/scripts',
            quickPayScript: 'Webkul_QuickPayForOrder/order/create/quickPayScript'
        }
    }
};