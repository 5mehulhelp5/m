<?php
declare(strict_types=1);

namespace CyberSolutionsLLC\CheckoutDefaultCountry\Plugin\Checkout\Block;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;

class DefaultCountryPlugin
{
    private string $defaultCountry;

    public function __construct(DirectoryHelper $directoryHelper)
    {
        $this->defaultCountry = $directoryHelper->getDefaultCountry() ?: 'US';
    }

    public function afterProcess(LayoutProcessorInterface $subject, array $jsLayout): array
    {
        // Cart page: estimate shipping block
        if (isset($jsLayout['components']['block-summary']['children']['block-shipping']
            ['children']['address-fieldsets']['children']['country_id'])) {
            $jsLayout['components']['block-summary']['children']['block-shipping']
                ['children']['address-fieldsets']['children']['country_id']['visible'] = false;
            $jsLayout['components']['block-summary']['children']['block-shipping']
                ['children']['address-fieldsets']['children']['country_id']['value']   = $this->defaultCountry;
        }

        // Checkout page: shipping address
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['country_id'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['country_id']['visible'] = false;
            $jsLayout['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['country_id']['value']   = $this->defaultCountry;
        }

        // Checkout page: per-payment billing forms
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['payments-list']['children'])) {
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['payments-list']['children'] as &$payment) {
                if (isset($payment['children']['form-fields']['children']['country_id'])) {
                    $payment['children']['form-fields']['children']['country_id']['visible'] = false;
                    $payment['children']['form-fields']['children']['country_id']['value']   = $this->defaultCountry;
                }
            }
            unset($payment);
        }

        // Checkout page: shared billing form (displayBillingOnPaymentMethod = false)
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children']
            ['billing-address-form']['children']['form-fields']['children']['country_id'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['afterMethods']['children']
                ['billing-address-form']['children']['form-fields']['children']['country_id']['visible'] = false;
            $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['afterMethods']['children']
                ['billing-address-form']['children']['form-fields']['children']['country_id']['value']   = $this->defaultCountry;
        }

        return $jsLayout;
    }
}
