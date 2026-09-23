define([
    'mage/utils/wrapper',
    'Magento_Ui/js/model/messageList'
], function (wrapper, globalMessageList) {
    'use strict';

    return function (storage) {
        storage.post = wrapper.wrapSuper(storage.post, function (url, data, global, contentType, headers) {
            var deferred, isEstimate;

            isEstimate = url.indexOf('estimate-shipping-methods') !== -1;

            if (!global && isEstimate) {
                global = true;
            }

            deferred = this._super(url, data, global, contentType, headers);

            if (isEstimate) {
                deferred.done(function (methods) {
                    if (Array.isArray(methods) && methods.length > 0) {
                        globalMessageList.clear();
                    }
                });
            }

            return deferred;
        });

        return storage;
    };
});
