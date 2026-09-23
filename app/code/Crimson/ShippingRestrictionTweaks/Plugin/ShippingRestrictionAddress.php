<?php declare(strict_types=1);

namespace Crimson\ShippingRestrictionTweaks\Plugin;

use Crimson\ShippingRestrictionTweaks\Model\Registry\RestrictionResult;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResource;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ShippingRestriction\Helper\Data as HelperData;
use Mageplaza\ShippingRestriction\Plugin\Model\Quote\Address as BaseAddress;

class ShippingRestrictionAddress extends BaseAddress
{
    protected RestrictionResult $restrictionResult;

    /**
     * The quote shipping address currently being filtered, captured in
     * afterGetGroupedAllShippingRates() so processShippingMethod() can decide
     * whether the address is complete enough to hold a restriction responsible
     * for an empty rate list.
     */
    protected ?QuoteAddress $currentAddress = null;

    public function __construct(
        Registry $coreRegistry,
        RequestInterface $request,
        TotalsCollector $totalsCollector,
        CartRepositoryInterface $cartRepository,
        BackendSession $backendSession,
        QuoteSession $quoteSession,
        AddressRepositoryInterface $addressRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteIdMaskResource $quoteIdMaskResource,
        HelperData $helperData,
        StoreManagerInterface $_store,
        RestrictionResult $restrictionResult,
        DataObjectProcessor $dataProcessor = null
    ) {
        $this->restrictionResult = $restrictionResult;
        parent::__construct(
            $coreRegistry,
            $request,
            $totalsCollector,
            $cartRepository,
            $backendSession,
            $quoteSession,
            $addressRepository,
            $quoteIdMaskFactory,
            $quoteIdMaskResource,
            $helperData,
            $_store,
            $dataProcessor
        );
    }

    /**
     * Plugin instances are shared for the whole request and getGroupedAllShippingRates()
     * runs more than once per estimate. The parent resets $ruleActive but not
     * $appliedRule, so a rule matched on an earlier pass would otherwise leak into a
     * later pass where nothing matched. Clear both before delegating.
     *
     * @param QuoteAddress $subject
     * @param array $result
     * @return array
     */
    public function afterGetGroupedAllShippingRates(QuoteAddress $subject, $result)
    {
        $this->appliedRule = null;
        $this->restrictionResult->reset();
        $this->currentAddress = $subject;

        return parent::afterGetGroupedAllShippingRates($subject, $result);
    }

    /**
     * Record whether this restriction is what emptied the rate list. Only when rates
     * existed and none survived is it accurate to tell the customer that a shipping
     * restriction is the reason they have no options.
     *
     * On an incomplete shipping address (the normal state when the checkout first
     * loads before the customer has typed anything) an empty rate list is expected
     * and must never be blamed on a restriction rule — otherwise the rule's message
     * (e.g. "Restrict Flat Rate" / "Sorry, we can't ship to that address") would
     * surface immediately on page load, before any address exists.
     *
     * @param array $shippingRatesCol
     * @param array $appliedShipMethod
     */
    public function processShippingMethod(&$shippingRatesCol, $appliedShipMethod)
    {
        $before = $this->countRates($shippingRatesCol);
        parent::processShippingMethod($shippingRatesCol, $appliedShipMethod);
        $after = $this->countRates($shippingRatesCol);

        if (!$this->isAddressComplete()) {
            return;
        }

        $this->restrictionResult->recordFiltering($this->appliedRule, $before, $after);
    }

    /**
     * A shipping address is only considered complete enough to blame a restriction
     * rule for removing every method once it has a country and at least a region or
     * postcode — the fields the customer has to fill in before rates can be real.
     *
     * @return bool
     */
    private function isAddressComplete(): bool
    {
        $address = $this->currentAddress;
        if ($address === null) {
            return false;
        }

        if (!$address->getCountryId()) {
            return false;
        }

        $regionId = (string) $address->getRegionId();
        $region = trim((string) $address->getRegion());
        $postcode = trim((string) $address->getPostcode());
        $city = trim((string) $address->getCity());

        $hasLocality = $regionId !== '' || $region !== '' || $postcode !== '';
        $hasStreet = false;
        $street = $address->getStreet();
        if (is_array($street)) {
            foreach ($street as $line) {
                if (is_string($line) && trim($line) !== '') {
                    $hasStreet = true;
                    break;
                }
            }
        } elseif (is_string($street) && trim($street) !== '') {
            $hasStreet = true;
        }

        return $hasLocality && $hasStreet && $city !== '';
    }

    /**
     * Total number of rates across all carrier groups.
     *
     * @param mixed $shippingRatesCol
     * @return int
     */
    private function countRates($shippingRatesCol): int
    {
        if (!is_array($shippingRatesCol)) {
            return 0;
        }

        $count = 0;
        foreach ($shippingRatesCol as $rates) {
            if (is_array($rates) || $rates instanceof \Countable) {
                $count += count($rates);
            }
        }

        return $count;
    }
}
