<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardExtension\Invoice;

class InvoiceExtensionRegistry
{
    /**
     * @var Invoice|null
     */
    protected $gCardInvoice = null;

    /**
     * @param Invoice $gCardInvoice
     */
    public function setCurrentGiftCardInvoice(Invoice $gCardInvoice)
    {
        $this->gCardInvoice = $gCardInvoice;
    }

    /**
     * @return Invoice|null
     */
    public function getCurrentGiftCardInvoice()
    {
        return $this->gCardInvoice;
    }

    public function _resetState(): void
    {
        $this->gCardInvoice = null;
    }
}
