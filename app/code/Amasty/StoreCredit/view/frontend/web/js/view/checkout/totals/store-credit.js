define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals',
    'uiRegistry'
], function (Component, totals, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_StoreCredit/checkout/totals/storecredit'
        },
        totals: totals.totals(),
        getValue: function () {
            return this.getFormattedPrice(this.getAmstoreCreditValue());
        },
        getAmstoreCreditValue: function () {
            var price = 0,
                priceMax = 0,
                segment,
                maxSegment;

            if (this.totals) {
                segment = totals.getSegment('amstorecredit');

                if (segment) {
                    price = segment.value;
                    registry.async('index = storecredit')(function (SC) {
                        price = Math.abs(price);
                        if (SC.isApplied() && price < SC.appliedAmount) {
                            var sumRest = SC.amount() - price;
                            SC.amount(price);
                            SC.appliedAmount = price;
                            SC.available(SC.available() + sumRest);
                        }
                    })
                }

                maxSegment = totals.getSegment('amstorecredit_max');

                if (maxSegment) {
                    priceMax = maxSegment.value;
                    registry.async('index = storecredit')(function (SC) {
                        SC.amount(priceMax);
                    })
                }
            }

            return price;
        },
        isAvailable: function () {
            return this.isFullMode() && this.getAmstoreCreditValue() != 0;
        }
    });
});
