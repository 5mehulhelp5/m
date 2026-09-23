<?php
declare(strict_types=1);

namespace CyberSolutionsLLC\CheckoutDefaultCountry\Plugin\Quote;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Quote\Model\ShippingMethodManagement;

class ShippingMethodManagementPlugin
{
    private string $defaultCountry;

    public function __construct(DirectoryHelper $directoryHelper)
    {
        $this->defaultCountry = $directoryHelper->getDefaultCountry() ?: 'US';
    }

    public function beforeEstimateByAddress(
        ShippingMethodManagement $subject,
        $cartId,
        EstimateAddressInterface $address
    ): array {
        if (!$address->getCountryId()) {
            $address->setCountryId($this->defaultCountry);
        }
        return [$cartId, $address];
    }

    public function beforeEstimateByExtendedAddress(
        ShippingMethodManagement $subject,
        $cartId,
        AddressInterface $address
    ): array {
        if (!$address->getCountryId()) {
            $address->setCountryId($this->defaultCountry);
        }
        return [$cartId, $address];
    }
}
