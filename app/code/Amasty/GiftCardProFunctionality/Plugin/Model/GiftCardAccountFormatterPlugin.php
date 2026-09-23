<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Plugin\Model;

use Amasty\GiftCard\Model\Config\Source\Usage;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardProFunctionality\Model\GiftCardAccount\Usage\Checker;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GiftCardAccountFormatterPlugin
{
    /**
     * @var Usage
     */
    private $usage;

    /**
     * @var Checker
     */
    private $usageChecker;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        Usage $usage,
        Checker $usageChecker,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->usage = $usage;
        $this->usageChecker = $usageChecker;
        $this->priceCurrency = $priceCurrency;
    }

    public function afterGetFormattedData($subject, $result, GiftCardAccountInterface $account)
    {
        $result['usage'] = $this->usage->getValueByKey($account->getUsage());
        if ($this->usageChecker->isSingleUsed($account)) {
            $result['balance'] = $this->getCurrentBalance(0);
        }

        return $result;
    }

    /**
     * @param float $price
     * @return string
     */
    protected function getCurrentBalance(float $price): string
    {
        return $this->priceCurrency->convertAndFormat($price);
    }
}
