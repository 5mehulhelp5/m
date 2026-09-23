<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Utils;

class AfterFilterApplier
{
    /**
     * @param array|string|int $condition
     * @param mixed $value
     * @param null|mixed $filterValue can be null if value is a part of array condition
     *
     * @return bool
     */
    public function apply($condition, $value, $filterValue): bool
    {
        if (is_array($condition)) {
            $result = true;
            foreach ($condition as $conditionName => $conditionValue) {
                if (is_int($conditionName) && is_array($conditionValue)) {
                    $result = false;
                    if ($this->apply($conditionValue, $value, null)) { // Logical OR
                        return true;
                    }
                } elseif (!$this->apply($conditionName, $value, $conditionValue)) { // Logical AND
                    return false;
                }
            }
        } else {
            switch ($condition) {
                case 'eq':
                    $result = $value == $filterValue;
                    break;
                case 'neq':
                    $result = $value != $filterValue;
                    break;
                case 'like':
                    $result = mb_stripos((string)$value, (string)$filterValue) !== false;
                    break;
                case 'nlike':
                    $result = mb_stripos((string)$value, (string)$filterValue) === false;
                    break;
                case 'in':
                    $result = in_array($value, (array)$filterValue);
                    break;
                case 'nin':
                    $result = !in_array($value, (array)$filterValue);
                    break;
                case 'notnull':
                    $result = $value !== null;
                    break;
                case 'null':
                    $result = $value === null;
                    break;
                case 'gt':
                    $result = $value > $filterValue;
                    break;
                case 'lt':
                    $result = $value < $filterValue;
                    break;
                case 'gteq':
                    $result = $value >= $filterValue;
                    break;
                case 'lteq':
                    $result = $value <= $filterValue;
                    break;
                case 'finset':
                    $result = in_array($filterValue, explode(',', (string)$value));
                    break;
                case 'nfinset':
                    $result = !in_array($filterValue, explode(',', (string)$value));
                    break;
                default:
                    $result = false;
            }
        }

        return $result;
    }
}
