/**
 * Save active tab and activate it after page reload
 */
require([
    'underscore',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/lib/view/utils/async'
], function (_, $, registry) {
    'use strict';

    /**
     * Activate tab
     * @param {String} tabName
     * @returns {void}
     */
    function activateTab(tabName) {
        var tab = registry.get(tabName);

        if (tab) {
            tab.activate();
        }
    }

    /**
     * Save active tab to storage
     * @returns {void}
     */
    function setTabToStorage() {
        var tabs = registry.get('customer_form.sections');

        _.each(tabs.elems(), function (tab) {
            if (tab.active() === true) {
                window.localStorage.setItem('amasty-storecredit-active-tab', JSON.stringify(tab.name));
            }
        });
    }

    /**
     * Delete saved tab id from localstorage
     * @returns {void}
     */
    function clearTabStorage() {
        window.localStorage.removeItem('amasty-storecredit-active-tab');
    }

    $('#save_and_continue').on('click', setTabToStorage);

    $('#back,'
        + '#login_as_customer,'
        + '#customer-edit-delete-button,'
        + '#reset,'
        + '#order,'
        + '#resetPassword,'
        + '#invalidateToken,'
        + '#save').on('click', clearTabStorage);

    $.async('.admin__page-nav', function () {
        var storageTabName = JSON.parse(window.localStorage.getItem('amasty-storecredit-active-tab'));

        if (storageTabName) {
            activateTab(storageTabName);
        }
    });
});
