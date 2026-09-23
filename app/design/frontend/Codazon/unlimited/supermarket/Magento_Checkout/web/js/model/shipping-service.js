/**
 * Crimson Agility, LLC
 */

define([
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/action/set-shipping-information'
], function (ko, checkoutDataResolver, selectShippingMethodAction, setShippingInformationAction) {
    'use strict';

    var shippingRates = ko.observableArray([]);
    var firstLoad = true;

    return {
        isLoading: ko.observable(false),

        /**
         * Set shipping rates
         *
         * @param {*} ratesData
         */
        setShippingRates: function (ratesData) {
            shippingRates(ratesData);
            shippingRates.valueHasMutated();
            checkoutDataResolver.resolveShippingRates(ratesData);

            if (ratesData.length >= 1 && firstLoad) {
                firstLoad = false;
                //set first shipping method option if available
                selectShippingMethodAction(ratesData[0]);
                //Then set the shipping information action
                setShippingInformationAction();
            }
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getShippingRates: function () {
            return shippingRates;
        }
    };
});
