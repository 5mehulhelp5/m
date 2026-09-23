<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Plugin\Sales\Model;

use Magento\Sales\Model\Order;

/**
 * Consider Store Credit Amount For Refund
 */
class OrderPlugin
{
    public const EPSILON = 0.00001;

    public function afterCanCreditmemo(Order $subject, $result)
    {
        if (!$result || $subject->getAmstorecreditRefundedBaseAmount() === null) {
            return $result;
        }

        $totalInvoiced = $subject->getBaseTotalInvoiced()
            + $subject->getBaseRwrdCrrncyAmtInvoiced()
            + $subject->getBaseCustomerBalanceInvoiced()
            + $subject->getBaseGiftCardsInvoiced()
            + $subject->getAmstorecreditInvoicedBaseAmount();
        $totalRefunded = $subject->getBaseTotalRefunded()
            + $subject->getBaseRwrdCrrncyAmntRefnded()
            + ($subject->getBaseCustomerBalanceInvoiced() ? $subject->getBaseCustomerBalanceRefunded() : 0)
            + $subject->getBaseGiftCardsRefunded()
            + $subject->getAmstorecreditRefundedBaseAmount();

        if ($this->isGreater($totalInvoiced, $totalRefunded)) {
            return true;
        }

        $itemsQtyRefunded = 0;
        foreach ($subject->getAllItems() as $item) {
            $itemsQtyRefunded += $item->getQtyRefunded();
        }

        if ($this->isGreaterThanOrEqual($totalRefunded, (float) $subject->getBaseTotalPaid())
            && $subject->getTotalQtyOrdered() <= $itemsQtyRefunded
        ) {
            return false;
        }

        return $result;
    }

    /**
     * Compares two float digits.
     *
     * @param float $a
     * @param float $b
     *
     * @return bool
     * @since 101.0.6
     */
    private function isEqual(float $a, float $b): bool
    {
        return abs($a - $b) <= self::EPSILON;
    }

    /**
     * Compares if the first argument greater than the second argument.
     *
     * @param float $a
     * @param float $b
     *
     * @return bool
     * @since 101.0.6
     */
    private function isGreater(float $a, float $b): bool
    {
        return ($a - $b) > self::EPSILON;
    }

    /**
     * Compares if the first argument greater or equal to the second.
     *
     * @param float $a
     * @param float $b
     *
     * @return bool
     * @since 101.0.6
     */
    private function isGreaterThanOrEqual(float $a, float $b): bool
    {
        return $this->isEqual($a, $b) || $this->isGreater($a, $b);
    }
}
