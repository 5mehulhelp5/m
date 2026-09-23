/**
 * Copyright © CyberSolutions LLC. All rights reserved.
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer'
], function ($, wrapper, quote, customer) {
    'use strict';

    /**
     * The one-step checkout resolves the payment method from local storage
     * before a guest has entered their email address. The resulting
     * set-payment-information call is sent without an email and is rejected
     * by the web API with HTTP 400 ("email is required"), which surfaces as
     * a console error on every guest checkout load. Skipping the doomed
     * request changes nothing functionally: it is re-sent once the guest
     * email is validated, and place-order always sends it again.
     */
    return function (setPaymentInformationExtended) {
        return wrapper.wrap(setPaymentInformationExtended, function (original, messageContainer, paymentData, skipBilling) {
            if (!customer.isLoggedIn() && !quote.guestEmail) {
                return $.Deferred().resolve().promise();
            }
            return original(messageContainer, paymentData, skipBilling);
        });
    };
});