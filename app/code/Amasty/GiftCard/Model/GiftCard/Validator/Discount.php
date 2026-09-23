<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\GiftCard\Validator;

use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard;
use Laminas\Validator\ValidatorInterface;
use Magento\Quote\Model\Quote\Item;

class Discount implements ValidatorInterface
{
    /**
     * @var []
     */
    protected $messages;

    /**
     * Define if we can apply discount to current item
     *
     * @param Item $item
     * @return bool
     */
    public function isValid($item)
    {
        if (GiftCard::TYPE_AMGIFTCARD == $item->getProductType()) {
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
