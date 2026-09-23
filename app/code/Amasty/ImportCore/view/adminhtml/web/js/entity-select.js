define([
    'Amasty_Base/js/form/element/ui-promotion-select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            switchEntityUrl: '',
            entityUrl: '',
            indexUrl: ''
        },

        onUpdate: function () {
            this._super();

            if (this.value() !== '' && this.value() !== undefined) {
                location.href = this.entityUrl.replace('__entity_code__', this.value());
            } else {
                location.href = this.indexUrl;
            }
        },

        toggleOptionSelected: function (data) {
            if (data.hasOwnProperty(this.separator)) {
                this.openChildLevel(data);
                return;
            }

            return this._super();
        },

        _getFilteredArray: function (list, value) {
            var i = 0,
                array = [],
                curOption;

            for (i; i < list.length; i++) {
                curOption = list[i].label.toLowerCase();

                if ((curOption.indexOf(value) > -1) && (list[i].path !== '')) {
                    array.push(list[i]); /*eslint max-depth: [2, 4]*/
                }
            }

            return array;
        }
    });
});
