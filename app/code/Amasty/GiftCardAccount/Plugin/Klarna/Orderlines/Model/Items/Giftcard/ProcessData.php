<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Plugin\Klarna\Orderlines\Model\Items\Giftcard;

use Klarna\Base\Model\Api\DataHolder;
use Klarna\Base\Model\Api\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder as ContainerDataHolder;
use Klarna\Orderlines\Model\Container\Parameter as ContainerParameter;
use Klarna\Orderlines\Model\ItemGenerator;
use Klarna\Orderlines\Model\Items\Giftcard;
use Klarna\Orderlines\Model\Items\Giftcard\Handler;
use Magento\Quote\Api\Data\CartInterface;

class ProcessData
{
    /**
     * @var array
     */
    private $data;

    /**
     * compatibility with different klarna versions.
     * @param Giftcard|Handler $subject
     * @param Giftcard|Handler $result
     * @param Parameter|ContainerParameter $checkout
     * @return Giftcard|Handler
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterFetch($subject, $result, $checkout)
    {
        if (isset($this->data['total_amount'])) {
            $checkout->addOrderLine(
                [
                    'type' => ItemGenerator::ITEM_TYPE_GIFTCARD,
                    'reference' => $this->data['reference'] ?? '',
                    'name' => $this->data['title'] ?? '',
                    'quantity' => 1,
                    'unit_price' => $this->data['unit_price'] ?? 0,
                    'tax_rate' => 0,
                    'total_amount' => $this->data['total_amount'] ?? 0,
                    'total_tax_amount' => 0,
                ]
            );
        }

        return $subject;
    }

    /**
     * @param Giftcard|Handler $subject
     * @param Giftcard|Handler $result
     * @param Parameter|ContainerParameter $parameter
     * @param DataHolder|ContainerDataHolder $dataHolder
     * @param CartInterface $quote
     * @return Giftcard|Handler
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCollectPrePurchase($subject, $result, $parameter, $dataHolder, $quote)
    {
        $this->collect($dataHolder, $quote);

        return $subject;
    }

    private function collect($dataHolder, CartInterface $quote): void
    {
        $totals = $dataHolder->getTotals();

        if (!is_array($totals) || !isset($totals['amgiftcard'])) {
            return;
        }
        $total = $totals['amgiftcard'];

        if ($total->getValue() !== 0
            && $quote->getExtensionAttributes()
            && $quote->getExtensionAttributes()->getAmGiftcardQuote()
        ) {
            $gCardQuote = $quote->getExtensionAttributes()->getAmGiftcardQuote();
            $value = -1 * $this->toApiFloat($gCardQuote->getGiftAmountUsed());

            $this->data = [
                'unit_price' => $value,
                'total_amount' => $value,
                'title' => $total->getTitle(),
                'reference' => $total->getCode()
            ];
        }
    }

    private function toApiFloat(float $float): float
    {
        return round($float * 100);
    }
}
