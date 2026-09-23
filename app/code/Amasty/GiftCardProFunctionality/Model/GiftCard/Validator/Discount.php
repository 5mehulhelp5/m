<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model\GiftCard\Validator;

use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard;
use Amasty\GiftCardProFunctionality\Model\ConfigProvider;
use Laminas\Validator\ValidatorInterface;
use Magento\Quote\Model\Quote\Item;

class Discount implements ValidatorInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * Define if we can apply discount to current item
     *
     * @param Item $item
     * @return bool
     */
    public function isValid($item)
    {
        if (GiftCard::TYPE_AMGIFTCARD == $item->getProductType()
            && !$this->configProvider->isCartPriceRuleForAmGiftCard()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return [];
    }
}
