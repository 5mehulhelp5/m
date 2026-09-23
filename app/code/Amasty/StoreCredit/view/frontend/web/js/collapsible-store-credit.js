define([
    'jquery',
    'mage/collapsible'
], function ($) {
    'use strict';

    $.widget('amstorecredit.collapsible', $.mage.collapsible, {
        _create: function () {
            // WCAG compatibility
            this.options.content = $(this.element).find(this.options.content);
            this._super();
        }
    });

    return $.amstorecredit.collapsible;
});
