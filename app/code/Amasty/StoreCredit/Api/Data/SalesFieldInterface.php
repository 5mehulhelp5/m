<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Api\Data;

interface SalesFieldInterface
{
    public const AMSC_USE = 'amstorecredit_use';
    public const AMSC_BASE_AMOUNT = 'amstorecredit_base_amount';
    public const AMSC_AMOUNT = 'amstorecredit_amount';
    public const AMSC_SHIPPING_AMOUNT = 'amstorecredit_shipping_amount';
    public const AMSC_SHIPPING_AMOUNT_INVOICED = 'amstorecredit_shipping_amount_invoiced';
    public const AMSC_SHIPPING_AMOUNT_REFUNDED = 'amstorecredit_shipping_amount_refunded';
    public const AMSC_INVOICED_BASE_AMOUNT = 'amstorecredit_invoiced_base_amount';
    public const AMSC_INVOICED_AMOUNT = 'amstorecredit_invoiced_amount';
    public const AMSC_REFUNDED_BASE_AMOUNT = 'amstorecredit_refunded_base_amount';
    public const AMSC_REFUNDED_AMOUNT = 'amstorecredit_refunded_amount';
}
