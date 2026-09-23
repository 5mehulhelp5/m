<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;

class TimezoneOffsetOptions implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $result = [];

        foreach ($this->toArray() as $label => $value) {
            $result[] = ['label' => $label, 'value' => $value];
        }

        return $result;
    }

    public function toArray(): array
    {
        $timezoneOffsets = [];
        foreach ($this->getOffsetsList() as $offset) {
            if ($offset == 0) {
                $sign = '±';
            } else {
                $sign = ($offset > 0) ? '+' : '-';
            }
            $utcOffset = gmdate('H:i', abs($offset));
            $timezoneOffsets['UTC' . $sign . $utcOffset] = $offset;
        }

        return $timezoneOffsets;
    }

    private function getOffsetsList(): array
    {
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC) ?: [];
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $offsetsList = [];
        foreach ($timezoneIdentifiers as $identifier) {
            try {
                $currentTimezone = new \DateTimeZone($identifier);
            } catch (\Exception $e) {
                continue;
            }
            $offset = $currentTimezone->getOffset($utcTime);
            if (!in_array($offset, $offsetsList)) {
                $offsetsList[] = $offset;
            }
        }
        sort($offsetsList);

        return $offsetsList;
    }
}
