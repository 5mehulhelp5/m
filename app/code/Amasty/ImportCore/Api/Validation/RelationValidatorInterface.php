<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Validation;

interface RelationValidatorInterface
{
    /**
     * Validate entity row
     *
     * @param array $entityRow
     * @param array $subEntityRows
     * @return bool
     */
    public function validate(array $entityRow, array $subEntityRows): bool;

    /**
     * Get validation message
     *
     * @return string|null
     */
    public function getMessage(): ?string;
}
