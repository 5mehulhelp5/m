define([
    "jquery"
], function ($) {

    $.widget('mage.amgiftOrder', {
        options: {
            selectors: {
                parent: '.order-details',
                apply: '[data-amgiftcard="apply"]',
                remove: '[data-amgiftcard="remove"]',
                code: '#amgiftcard_code'
            }
        },

        /**
         * @returns {void}
         */
        _init: function () {
            $(this.options.selectors.parent)
                .off('click', this.options.selectors.apply)
                .on('click', this.options.selectors.apply, this.applyAmGCard.bind(this));
            $(this.options.selectors.parent)
                .off('click', this.options.selectors.remove)
                .on('click', this.options.selectors.remove, this.removeAmGCard.bind(this));
        },

        /**
         * @returns {void}
         */
        applyAmGCard: function () {
            let data = {};
            data['amgiftcard_add'] = $(this.options.selectors.code).val();
            order.loadArea(
                ['items', 'shipping_method', 'totals', 'billing_method'],
                true,
                data
            );
        },

        /**
         * @param {Object} event
         * @returns {void}
         */
        removeAmGCard: function ({ currentTarget }) {
            const code = currentTarget.dataset.amgiftcardRemoveCode ?? '';
            let data = {};

            data['amgiftcard_remove'] = code;
            order.loadArea(
                ['items', 'shipping_method', 'totals', 'billing_method'],
                true,
                data
            );
        }
    });

    return $.mage.amgiftOrder;
});
