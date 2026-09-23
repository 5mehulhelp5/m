<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\StoreCredit;

use Magento\Sales\Api\Data\CreditmemoInterface;

class RefundCreditStorage
{
    /**
     * @var float
     */
    private $baseAmount = 0.0;

    /**
     * @var CreditmemoInterface
     */
    private $currentCreditMemo;

    public function getBaseAmount(): float
    {
        return $this->baseAmount;
    }

    public function setBaseAmount(float $amount): void
    {
        $this->baseAmount = $amount;
    }

    public function getCurrentCreditMemo(): CreditmemoInterface
    {
        return $this->currentCreditMemo;
    }

    public function setCurrentCreditMemo(CreditmemoInterface$creditMemo): void
    {
        $this->currentCreditMemo = $creditMemo;
    }
}
