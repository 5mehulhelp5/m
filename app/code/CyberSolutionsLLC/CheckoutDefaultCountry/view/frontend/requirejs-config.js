var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/postcode-validator': {
                'CyberSolutionsLLC_CheckoutDefaultCountry/js/model/postcode-validator-mixin': true
            },
            'Magento_Checkout/js/model/default-validator': {
                'CyberSolutionsLLC_CheckoutDefaultCountry/js/model/default-validator-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information-extended': {
                'CyberSolutionsLLC_CheckoutDefaultCountry/js/model/set-payment-information-guest-email-mixin': true
            }
        }
    }
};
