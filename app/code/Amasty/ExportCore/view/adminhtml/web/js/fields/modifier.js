define([
    'uiCollection',
    'underscore',
    'uiLayout',
    'mageUtils'
], function (Collection, _, layout, utils) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Amasty_ExportCore/fields/modifier',
            links: {
                select_value: '${ $.provider }:${ $.dataScope }.select_value',
                eavEntityType: '${ $.provider }:${ $.dataScope }.eavEntityType',
                optionSource: '${ $.provider }:${ $.dataScope }.optionSource',
                identity: '${ $.provider }:${ $.dataScope }.identity'
            },
            listens: {
                select_value: 'getModifierTypeSelected',
                selectedOption: 'createModifierField'
            },
            selectedType: null,
            selectedOption: {},
            options: [],
            selectedActions: [],
            modifierValue: {}
        },

        initialize: function () {
            this._super();

            this.renderFields();

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'eavEntityType',
                'select_value',
                'code',
                'selectedType',
                'selectedOption',
                'optionSource',
                'identity'
            ]);

            return this;
        },

        renderFields: function () {
            var name = this.name + '.value',
                value = this.getInitValue(),
                componentData = utils.extend(value, {
                    'parentName': this.name,
                    'provider': this.provider,
                    'dataScope': this.dataScope + '.value',
                    'prefer': 'toggle',
                    'parentScope': this.dataScope,
                    'source': this.source,
                    'options': this.options,
                    'name': name,
                    'component': this.childComponent,
                    'template': this.childTemplate,
                    'additionalData': this.additionalData
                });

            layout([componentData]);
            this.insertChild(name);
        },

        getInitValue: function () {
            if (this.additionalData && this.additionalData.default) {
                if (this.modifierValue && !this.modifierValue.input_value) {
                    this.modifierValue.input_value = this.additionalData.default;
                }
            }

            return this.modifierValue || {};
        },

        getModifierTypeSelected: function (value) {
            var option;

            this.options.forEach(function (optgroup) {
                option = this.findValue(optgroup, value);

                if (option) {
                    this.selectedType(optgroup.type);
                    this.selectedOption(option);
                }
            }.bind(this));
        },

        createModifierField: function (option) {
            _.each(this.elems(), function (element) {
                element.destroy();
            }, this);

            this.childTemplate = this.modifierConfig[option.value].childTemplate || null;
            this.childComponent = this.modifierConfig[option.value].childComponent || null;
            //additional data that can be used in templates
            this.additionalData = this.modifierConfig[option.value].additionalData || {};
            this.eavEntityType(this.selectedOption().eavEntityType);
            this.optionSource(this.selectedOption().optionSource);
            this.identity(this.selectedOption().identity);

            if (this.childComponent && this.childTemplate) {
                this.renderFields();
            }
        },

        findValue: function (optgroup, value) {
            return optgroup.value.find(function (item) {
                return item.value === value;
            });
        },

        remove: function () {
            this.source.remove(this.dataScope);
            this.destroy();
        },

        setDefaultValue: function () {
            this.select_value(this.selectValue);
        }
    });
});
