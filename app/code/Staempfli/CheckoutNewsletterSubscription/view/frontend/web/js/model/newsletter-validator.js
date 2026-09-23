define(
    [
        'jquery',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/error-processor',
        'mage/url'

    ],
    function ($, customer, quote, urlBuilder, errorProcessor, urlFormatter) {
        'use strict';

        return {
            validate: function () {
                var isCustomer = customer.isLoggedIn();
                // var status = $('#newsletter-subscription').is(':checked');
                var status;
                if ($('.opc-sidebar.opc-summary-wrapper #newsletter-subscription').prop("checked") == true) {
                    status = true;
                } else {
                    status = false;
                }
                var quoteId = quote.getQuoteId();
                var url;

                if (isCustomer) {
                    url = urlBuilder.createUrl('/carts/mine/newsletter-subscribe', {})
                } else {
                    url = urlBuilder.createUrl('/guest-carts/:cartId/newsletter-subscribe', { cartId: quoteId });
                }
                console.log(isCustomer);
                console.log(status);
                console.log(url);
                var payload = {
                    cartId: quoteId,
                    newsletterSubscription: {
                        subscribe: status
                    }
                };
                console.log(payload);

                if (!payload.newsletterSubscription.subscribe) {
                    return true;
                }

                var result = true;

                $.ajax({
                    url: urlFormatter.build(url),
                    data: JSON.stringify(payload),
                    global: false,
                    contentType: 'application/json',
                    type: 'PUT',
                    async: false
                }).done(
                    function () {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );
                return result;
            }
        };
    }
);