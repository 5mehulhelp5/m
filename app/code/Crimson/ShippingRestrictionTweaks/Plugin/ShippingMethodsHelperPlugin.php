<?php declare(strict_types=1);

namespace Crimson\ShippingRestrictionTweaks\Plugin;

use Magento\Shipping\Model\Config as ShippingConfig;
use Mageplaza\ShippingRestriction\Helper\Data;

/**
 * After the AC-15210 USPS REST API patch, Helper/Data::getShippingMethods() calls
 * getAllowedMethods() on each carrier, which for USPS only reads the legacy XML
 * `allowed_methods` config — ignoring the new `rest_allowed_methods` config when
 * the store is in USPS_REST mode.
 *
 * This plugin:
 * 1. Replaces the USPS carrier's methods with REST method codes+titles when
 *    usps_type = USPS_REST, so the Mageplaza ShippingRestriction rule admin UI
 *    shows the correct selectable methods.
 * 2. Filters out any remaining non-scalar labels to prevent "Array to string
 *    conversion" errors in the ShippingMethod renderer.
 */
class ShippingMethodsHelperPlugin
{
    private ShippingConfig $shippingConfig;

    public function __construct(ShippingConfig $shippingConfig)
    {
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * @param Data $subject
     * @param array $result
     * @return array
     */
    public function afterGetShippingMethods(Data $subject, array $result): array
    {
        $result = $this->injectUspsRestMethods($result);
        $result = $this->filterNonScalarLabels($result);

        return $result;
    }

    /**
     * When USPS is configured in REST mode, replace its empty/XML-based method list
     * with the REST method codes and titles from the carrier's getCode('rest_method').
     */
    private function injectUspsRestMethods(array $result): array
    {
        $carriers = $this->shippingConfig->getAllCarriers();
        if (!isset($carriers['usps'])) {
            return $result;
        }

        $uspsCarrier = $carriers['usps'];
        if ($uspsCarrier->getConfigData('usps_type') !== 'USPS_REST') {
            return $result;
        }

        $restMethods = $uspsCarrier->getCode('rest_method');
        if (!is_array($restMethods)) {
            return $result;
        }

        $restOptions = [];
        foreach ($restMethods as $code => $title) {
            if (is_scalar($title) && $code !== '') {
                $restOptions[] = ['value' => 'usps_' . $code, 'label' => (string) $title];
            }
        }

        foreach ($result as $key => $carrier) {
            if (($carrier['label'] ?? '') === $uspsCarrier->getConfigData('title')) {
                $result[$key]['value'] = $restOptions;
                break;
            }
        }

        return $result;
    }

    /**
     * Strip any method entries whose label is not scalar to prevent
     * "Array to string conversion" in the ShippingMethod renderer.
     */
    private function filterNonScalarLabels(array $result): array
    {
        foreach ($result as $carrierKey => $carrier) {
            if (!isset($carrier['value']) || !is_array($carrier['value'])) {
                continue;
            }
            foreach ($carrier['value'] as $methodKey => $method) {
                if (!isset($method['label']) || !is_scalar($method['label'])) {
                    unset($result[$carrierKey]['value'][$methodKey]);
                }
            }
        }

        return $result;
    }
}
