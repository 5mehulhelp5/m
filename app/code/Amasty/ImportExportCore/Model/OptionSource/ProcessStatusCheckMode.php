<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ProcessStatusCheckMode implements OptionSourceInterface
{
    public const PID = 0;
    public const STATUS = 1;

    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        return $result;
    }

    public function toArray(): array
    {
        return [
            self::PID => __('System Process ID'),
            self::STATUS => __('Statuses')
        ];
    }
}
