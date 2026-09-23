<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SendGiftCardEvent implements OptionSourceInterface
{
    public const INVOICED = 0;
    public const CREATED = 1;

    public function toOptionArray()
    {
        $optionArray = [];

        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    public function toArray(): array
    {
        return [
            self::INVOICED => __('Invoiced'),
            self::CREATED => __('Created')
        ];
    }
}
