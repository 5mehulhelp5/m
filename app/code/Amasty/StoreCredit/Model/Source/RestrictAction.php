<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RestrictAction implements OptionSourceInterface
{
    public const INCLUDE = 0;
    public const EXCLUDE = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::INCLUDE, 'label' => __('Include')],
            ['value' => self::EXCLUDE, 'label' => __('Exclude')]
        ];
    }
}
