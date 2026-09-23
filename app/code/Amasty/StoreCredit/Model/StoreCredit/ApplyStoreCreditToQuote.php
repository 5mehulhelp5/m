<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\StoreCredit;

use Amasty\StoreCredit\Api\ApplyStoreCreditToQuoteInterface;
use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Quote\Model\Quote;

class ApplyStoreCreditToQuote implements ApplyStoreCreditToQuoteInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param int $cartId
     * @param float $amount
     * @return float
     */
    public function apply($cartId, $amount)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $quote->setData(SalesFieldInterface::AMSC_USE, 1);
        $quote->setData(SalesFieldInterface::AMSC_AMOUNT, abs($amount));
        $quote->collectTotals();
        $this->cartRepository->save($quote);

        return $quote->getData(SalesFieldInterface::AMSC_AMOUNT);
    }

    /**
     * @param int $cartId
     * @return float
     */
    public function cancel($cartId)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $quote->setData(SalesFieldInterface::AMSC_USE, 0);
        $quote->collectTotals();
        $this->cartRepository->save($quote);

        return $quote->getData(SalesFieldInterface::AMSC_AMOUNT);
    }
}
