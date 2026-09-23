<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Config\Source;

class Fee extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public const PRICE_TYPE_PERCENT = 1;
    public const PRICE_TYPE_FIXED = 2;

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            ['value' => self::PRICE_TYPE_PERCENT, 'label' => __('Percent')],
            ['value' => self::PRICE_TYPE_FIXED, 'label' => __('Fixed')],
        ];
    }
}
