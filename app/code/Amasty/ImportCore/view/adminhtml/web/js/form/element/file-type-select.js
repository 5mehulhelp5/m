define([
    'Amasty_ImportCore/js/type-selector',
    'jquery',
    'underscore',
    'knockout'
], function (Select, $, _, ko) {
    'use strict';

    return Select.extend({
        defaults: {
            fileProviderPath: '',
            dependedOptionsMap: [],
            listens: {
                fileSourceTypeValue: 'updateFileTypes'
            },
            imports: {
                fileSourceTypeValue: '${ $.fileProviderPath }.file_source_type:value'
            },
            disabledOptions: []
        },

        initObservable: function () {
            this._super().observe({disabledOptions: []});

            return this;
        },

        initialize: function () {
            this._super();
            this.updateFileTypes(this.fileSourceTypeValue);

            return this;
        },

        prepareOptions: function (disabledOptions) {
            return this.options.map(function (option) {
                option.disabled = disabledOptions.hasOwnProperty(option.value);
                return option;
            }.bind(this));
        },

        setOptionDisable: function(option, item) {
            if (item) {
                ko.applyBindingsToNode(option, {disable: item.disabled}, item);
            }
        },

        updateFileTypes: function (value) {
            var listDisabledOptions = this.dependedOptionsMap[value] ?? [];
            this.disabledOptions(this.prepareOptions(listDisabledOptions));
        }
    });
});
