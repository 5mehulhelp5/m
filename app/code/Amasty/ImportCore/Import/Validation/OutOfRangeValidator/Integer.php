<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Validation\OutOfRangeValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;

class Integer implements FieldValidatorInterface
{
    public const MAX_VALUE = 2147483647;
    public const MIN_VALUE = -2147483648;

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            return self::MIN_VALUE >= (int)$row[$field] && self::MAX_VALUE <= (int)$row[$field];
        }

        return true;
    }
}
