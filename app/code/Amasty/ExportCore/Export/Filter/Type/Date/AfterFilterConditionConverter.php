<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Date;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class AfterFilterConditionConverter
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    public function __construct(
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
    }

    public function convert(?string $filterCondition, ?string $filterValue): array
    {
        switch ($filterCondition) {
            case 'eq':
                $condition[] = ['gteq' => $this->getFromTimestamp((string)$filterValue)];
                $condition[] = ['lteq' => $this->getToTimestamp((string)$filterValue)];
                break;
            case 'neq':
                $condition[] = ['lt' => $this->getFromTimestamp((string)$filterValue)];
                $condition[] = ['gt' => $this->getToTimestamp((string)$filterValue)];
                break;
            case 'lteq':
            case 'gt':
                $condition[] = [$filterCondition => $this->getToTimestamp((string)$filterValue)];
                break;
            case 'lastXdays':
                $condition[] = ['gteq' => $this->getFromTimestamp((string)strtotime('-' . $filterValue . ' day'))];
                break;
            case 'lastXweeks':
                $condition[] = ['gteq' => $this->getFromTimestamp((string)strtotime('-' . $filterValue . ' week'))];
                break;
            case 'gteq':
            case 'lt':
            default:
                $condition[] = [$filterCondition => $this->getFromTimestamp((string)$filterValue)];
        }

        return $condition;
    }

    /**
     * Get timestamp including time as `00:00:00`
     *
     * @param string $filterValue
     * @return int|null
     */
    private function getFromTimestamp(string $filterValue): ?int
    {
        return $this->convertDate($filterValue);
    }

    /**
     * Get timestamp including time as `23:59:59`
     *
     * @param string $filterValue
     * @return int|null
     */
    private function getToTimestamp(string $filterValue): ?int
    {
        return $this->convertDate($filterValue, 23, 59, 59);
    }

    /**
     * Convert given date to timestamp
     *
     * @param string $date
     * @param int $hour
     * @param int $minute
     * @param int $second
     *
     * @return int|null
     */
    private function convertDate(string $date, int $hour = 0, int $minute = 0, int $second = 0): ?int
    {
        try {
            $dateObj = $this->localeDate->date($date, ConditionConverter::DEFAULT_SYSTEM_LOCALE, false);
            $dateObj->setTime($hour, $minute, $second);

            return $dateObj->getTimestamp();
        } catch (\Exception $e) {
            return null;
        }
    }
}
