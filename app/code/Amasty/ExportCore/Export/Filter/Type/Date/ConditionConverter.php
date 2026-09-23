<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Date;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ConditionConverter
{
    public const DEFAULT_SYSTEM_LOCALE = 'en_US';

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    public function __construct(
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
    }

    /**
     * Convert a date condition to a date and time condition, if needed
     *
     * @param $filterCondition
     * @param $filterValue
     * @return array
     */
    public function convert($filterCondition, $filterValue): array
    {
        $condition = [];
        switch ($filterCondition) {
            case 'eq':
                $condition['from'] = $this->getFromDateTime($filterValue);
                $condition['to'] = $this->getToDateTime($filterValue);
                break;
            case 'neq':
                $condition[] = ['lt' => $this->getFromDateTime($filterValue)];
                $condition[] = ['gt' => $this->getToDateTime($filterValue)];
                break;
            case 'lteq':
            case 'gt':
                $condition[$filterCondition] = $this->getToDateTime($filterValue);
                break;
            case 'lastXdays':
                $condition['gteq'] = $this->getFromDateTime(strtotime('-' . $filterValue . ' day'));
                break;
            case 'lastXweeks':
                $condition['gteq'] = $this->getFromDateTime(strtotime('-' . $filterValue . ' week'));
                break;
            case 'gteq':
            case 'lt':
            default:
                $condition[$filterCondition] = $this->getFromDateTime($filterValue);
        }

        return $condition;
    }

    /**
     * Get date including time as `00:00:00` in UTC timezone
     *
     * @param $filterValue
     * @return string|null
     */
    private function getFromDateTime($filterValue): ?string
    {
        return $this->convertDate($filterValue);
    }

    /**
     * Get date including time as `23:59:59` in UTC timezone
     *
     * @param $filterValue
     * @return string|null
     */
    private function getToDateTime($filterValue): ?string
    {
        return $this->convertDate($filterValue, 23, 59, 59);
    }

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param mixed $date
     * @param int $hour
     * @param int $minute
     * @param int $second
     *
     * @return string|null
     */
    private function convertDate($date, int $hour = 0, int $minute = 0, int $second = 0): ?string
    {
        try {
            $dateObj = $this->localeDate->date($date, self::DEFAULT_SYSTEM_LOCALE, false);
            $dateObj->setTime($hour, $minute, $second);

            return $dateObj->format(DateTime::DATETIME_PHP_FORMAT);
        } catch (\Exception $e) {
            return null;
        }
    }
}
