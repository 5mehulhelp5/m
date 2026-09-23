<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Validation;

interface FieldValidatorInterface
{
    /**
     * Validate entity field value
     *
     * @param array $row
     * @param string $field
     * @return bool
     */
    public function validate(array $row, string $field): bool;
}
