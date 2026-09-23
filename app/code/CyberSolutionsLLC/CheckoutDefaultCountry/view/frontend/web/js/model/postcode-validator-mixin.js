define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';

    return function (target) {
        target.validate = wrapper.wrap(target.validate, function (originalAction, postCode, countryId, postCodesPatterns) {
            // shipping-rates-validator reads country via $('select[name="country_id"]:visible').val()
            // which returns undefined when the field is hidden. On the cart page, window.checkoutConfig
            // is not available either, so we fall back to the hardcoded store-only country 'US'.
            var effectiveCountryId = countryId
                || (window.checkoutConfig && window.checkoutConfig.defaultCountryId)
                || 'US';
            return originalAction(postCode, effectiveCountryId, postCodesPatterns);
        });
        return target;
    };
});
