<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Validation;

interface RowValidatorInterface
{
    /**
     * Validate entity row
     *
     * @param array $row
     * @return bool
     */
    public function validate(array $row): bool;

    /**
     * Get validation message
     *
     * @return string|null
     */
    public function getMessage(): ?string;
}
