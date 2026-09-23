<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Validation\ValueValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;

class Number implements FieldValidatorInterface
{
    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field]) && !empty($row[$field])) {
            return is_numeric($row[$field]);
        }

        return true;
    }
}
