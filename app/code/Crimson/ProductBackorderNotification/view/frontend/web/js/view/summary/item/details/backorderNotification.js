define(['uiComponent'], function (Component) {
    'use strict';

    var backorderNotificationData = window.checkoutConfig.backorderNotificationData;

    return Component.extend({
        defaults: {
            template: 'Crimson_ProductBackorderNotification/summary/item/details/backorderNotification'
        },
        displayArea: 'backorder-information',
        backorderNotificationData: backorderNotificationData,

        /**
         * @param {Object} item
         * @return {Array}
         */
        getBackorderNotificationItem: function (item) {
            if (this.backorderNotificationData[item['item_id']]) {
                return this.backorderNotificationData[item['item_id']];
            }

            return [];
        }
    });
});
