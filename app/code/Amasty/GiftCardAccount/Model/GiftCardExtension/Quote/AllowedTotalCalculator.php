<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardExtension\Quote;

use Amasty\GiftCard\Model\ConfigProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\EventManager;

class AllowedTotalCalculator
{
    public const EVENT_NAME = 'amgcard_allowed_subtotal_calculated';

    /**
     * @var bool
     */
    private $isTaxAllowed = false;

    /**
     * @var bool
     */
    private $isShippingAllowed = false;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        ConfigProvider $configProvider,
        ?EventManager $eventManager = null
    ) {
        $this->isShippingAllowed = $configProvider->isShippingPaidAllowed();
        $this->isTaxAllowed = $configProvider->isTaxPaidAllowed();
        $this->eventManager = $eventManager ?? ObjectManager::getInstance()->get(EventManager::class);
    }

    /**
     * @param DataObject $from
     *
     * @return float|string
     */
    public function getAllowedSubtotal(DataObject $from)
    {
        if (($this->isTaxAllowed) && ($this->isShippingAllowed)) {
            $value = $from->getSubtotal()
                + $from->getTaxAmount()
                + $from->getDiscountAmount()
                + $from->getShippingAmount()
                + $from->getDiscountTaxCompensationAmount();
        } elseif ((!$this->isTaxAllowed) && ($this->isShippingAllowed)) {
            $value = $from->getSubtotalWithDiscount()
                + $from->getDiscountTaxCompensationAmount()
                + $from->getShippingAmount()
                + $from->getShippingDiscountTaxCompensationAmount();
        } elseif (($this->isTaxAllowed) && (!$this->isShippingAllowed)) {
            $value = $from->getSubtotalWithDiscount()
                + $from->getDiscountTaxCompensationAmount()
                + $from->getShippingDiscountAmount()
                + $from->getTaxAmount()
                + $from->getShippingDiscountTaxCompensationAmount();
        } else {
            $value = $from->getSubtotalWithDiscount()
                + $from->getDiscountTaxCompensationAmount()
                + $from->getShippingDiscountAmount();
        }
        $this->eventManager->dispatch(
            self::EVENT_NAME,
            ['from_object' => $from, 'value' => &$value, 'is_base_price' => false]
        );

        return $value;
    }

    /**
     * @param DataObject $from
     *
     * @return float|string
     */
    public function getAllowedBaseSubtotal(DataObject $from)
    {
        if (($this->isTaxAllowed) && ($this->isShippingAllowed)) {
            $value = $from->getBaseSubtotal()
                + $from->getBaseTaxAmount()
                + $from->getBaseDiscountAmount()
                + $from->getBaseShippingAmount()
                + $from->getBaseDiscountTaxCompensationAmount();
        } elseif ((!$this->isTaxAllowed) && ($this->isShippingAllowed)) {
            $value = $from->getBaseSubtotalWithDiscount()
                + $from->getBaseDiscountTaxCompensationAmount()
                + $from->getBaseShippingAmount()
                + $from->getBaseShippingDiscountTaxCompensationAmount();
        } elseif (($this->isTaxAllowed) && (!$this->isShippingAllowed)) {
            $value = $from->getBaseSubtotalWithDiscount()
                + $from->getBaseDiscountTaxCompensationAmount()
                + $from->getBaseShippingDiscountAmount()
                + $from->getBaseTaxAmount()
                + $from->getBaseShippingDiscountTaxCompensationAmount();
        } else {
            $value = $from->getBaseSubtotalWithDiscount()
                + $from->getBaseDiscountTaxCompensationAmount()
                + $from->getBaseShippingDiscountAmount();
        }
        $this->eventManager->dispatch(
            self::EVENT_NAME,
            ['from_object' => $from, 'value' => &$value, 'is_base_price' => true]
        );

        return $value;
    }
}
