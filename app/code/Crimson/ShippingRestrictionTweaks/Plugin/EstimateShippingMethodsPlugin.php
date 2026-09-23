<?php declare(strict_types=1);

namespace Crimson\ShippingRestrictionTweaks\Plugin;

use Crimson\ShippingRestrictionTweaks\Model\Registry\RestrictionResult;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;

/**
 * When a shipping restriction rule has removed every available shipping method,
 * surface the rule's reason to the customer instead of leaving them with an empty
 * shipping step.
 *
 * An empty method list on its own is not enough to act on — it is the normal state
 * for an incomplete address on checkout load, and for a carrier API that returned
 * nothing. We only speak up when the filtering step confirmed a rule is at fault.
 */
class EstimateShippingMethodsPlugin
{
    private const GENERIC_MESSAGE = "Sorry, we can't ship to that address. If you feel this is an error, please contact us.";

    private RestrictionResult $restrictionResult;

    public function __construct(RestrictionResult $restrictionResult)
    {
        $this->restrictionResult = $restrictionResult;
    }

    /**
     * @param ShipmentEstimationInterface $subject
     * @param ShippingMethodInterface[] $methods
     * @return ShippingMethodInterface[]
     * @throws LocalizedException
     */
    public function afterEstimateByExtendedAddress(ShipmentEstimationInterface $subject, array $methods): array
    {
        return $this->assertNotBlocked($methods);
    }

    /**
     * @param ShippingMethodManagementInterface $subject
     * @param ShippingMethodInterface[] $methods
     * @return ShippingMethodInterface[]
     * @throws LocalizedException
     */
    public function afterEstimateByAddress(ShippingMethodManagementInterface $subject, array $methods): array
    {
        return $this->assertNotBlocked($methods);
    }

    /**
     * @param ShippingMethodManagementInterface $subject
     * @param ShippingMethodInterface[] $methods
     * @return ShippingMethodInterface[]
     * @throws LocalizedException
     */
    public function afterEstimateByAddressId(ShippingMethodManagementInterface $subject, array $methods): array
    {
        return $this->assertNotBlocked($methods);
    }

    /**
     * @param ShippingMethodInterface[] $methods
     * @return ShippingMethodInterface[]
     * @throws LocalizedException
     */
    private function assertNotBlocked(array $methods): array
    {
        if (!empty($methods)) {
            return $methods;
        }

        $rule = $this->restrictionResult->getBlockingRule();
        if ($rule === null) {
            return $methods;
        }

        // Only the description is customer-facing; the rule name is an admin label.
        $description = trim((string) $rule->getDescription());

        throw new LocalizedException(
            $description !== '' ? __($description) : __(self::GENERIC_MESSAGE)
        );
    }
}
