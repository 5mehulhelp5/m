define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';

    return function (target) {
        target.validate = wrapper.wrap(target.validate, function (originalAction, address) {
            // Because the country_id field is hidden on the UI, it may not be bound by Knockout.
            // We inject the defaultCountryId here to pass frontend validation.
            // The actual request sent to the server will be safely handled by the Backend Plugin.
            if (!address.country_id) {
                address.country_id = (window.checkoutConfig && window.checkoutConfig.defaultCountryId) || 'US';
            }
            if (!address.countryId) {
                address.countryId = address.country_id;
            }
            
            return originalAction(address);
        });

        return target;
    };
});
