<?php declare(strict_types=1);

namespace Crimson\ShippingRestrictionTweaks\Model\Registry;

use Mageplaza\ShippingRestriction\Model\Rule;

/**
 * Per-request record of whether a shipping restriction rule is what removed every
 * available shipping method.
 *
 * A rule merely *matching* the quote says nothing — most rules match on cart totals
 * alone and match on carts that never had rates to begin with (incomplete address,
 * carrier API failure). Only the filtering step can tell us the rule is at fault,
 * so only the filtering step writes here.
 */
class RestrictionResult
{
    private ?Rule $blockingRule = null;

    /**
     * Clear the verdict. Called before each round of rate filtering.
     */
    public function reset(): void
    {
        $this->blockingRule = null;
    }

    /**
     * Record the outcome of one filtering pass. The rule is only held responsible
     * when there were rates to remove and none survived.
     *
     * @param Rule|null $rule
     * @param int $before Number of rates before the rule was applied
     * @param int $after Number of rates left after the rule was applied
     */
    public function recordFiltering(?Rule $rule, int $before, int $after): void
    {
        $this->blockingRule = ($rule !== null && $before > 0 && $after === 0) ? $rule : null;
    }

    /**
     * The rule that removed every shipping method, or null if no rule is to blame.
     */
    public function getBlockingRule(): ?Rule
    {
        return $this->blockingRule;
    }
}
