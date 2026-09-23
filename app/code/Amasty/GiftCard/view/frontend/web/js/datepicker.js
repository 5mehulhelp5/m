/**
 * Datepicker logic
 */

define([
    'uiComponent',
    'jquery',
    'mage/translate',
    'mage/calendar'
], function (Component, $, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            timeValue: '',
            isSendLater: 0,
            timezones: [],
            datepickerSelector: '[data-amcard-js="datepicker"]',
            dataPickerInputSelector: 'input[name="is_date_delivery"]',
            datepickerId: '#ui-datepicker-div',
            amDatepickerClass: 'am-datepicker',
            preconfiguredValues: {},
            dateFormat: '',
            datepickerPlaceholder: '',
            datepickerSelectTitle: $t('Select timezone')
        },

        initObservable: function () {
            this._super().observe(['timeValue', 'isSendLater', 'timezones']);

            return this;
        },

        initDatepicker: function () {
            $(this.datepickerSelector).calendar({
                minDate: new Date(),
                showButtonPanel: true,
                currentText: $t('Go Today'),
                dateFormat: this.dateFormat,
                changeMonth: true,
                changeYear: true,
                showOn: 'focus'
            });
            $(this.datepickerSelector).attr('placeholder', this.datepickerPlaceholder);
            $(this.datepickerId).addClass(this.amDatepickerClass);
        },

        validateValue: function () {
            var currentDate = Date.now();
            var dateParts = this.timeValue.split("/");
            var correctTimeValue = +new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);

            if (correctTimeValue < currentDate) {
                $(this.datepickerSelector).datepicker('setDate', currentDate);
            }
        },

        getSheduleDeliveryType: function (name) {
            if (this.preconfiguredValues[name]) {
                this.isSendLater(+this.preconfiguredValues[name]);
            }
        },

        getPreconfiguredValue: function (name) {
            if (this.preconfiguredValues[name]) {
                return this.preconfiguredValues[name];
            }
        },

        handlePressEnter: function(element) {
            element.target.querySelector(this.dataPickerInputSelector).click();
        }
    });
});
