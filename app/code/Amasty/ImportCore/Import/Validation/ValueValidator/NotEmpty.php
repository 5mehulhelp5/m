<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Validation\ValueValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;

class NotEmpty implements FieldValidatorInterface
{
    public const IS_ZERO_VALUE_ALLOWED = 'isZeroValueAllowed';

    public const DEFAULT_SETTINGS = [
        self::IS_ZERO_VALUE_ALLOWED => false
    ];

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = array_merge(self::DEFAULT_SETTINGS, $config);
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $value = trim((string)$row[$field]);
            $isZeroValueAllowed = $this->config[self::IS_ZERO_VALUE_ALLOWED] ?? false;
            if ($isZeroValueAllowed && (int)$value == 0) {
                return true;
            }

            return !empty($value);
        }

        return false;
    }
}
