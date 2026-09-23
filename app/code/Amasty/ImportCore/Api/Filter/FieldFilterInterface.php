<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Filter;

interface FieldFilterInterface
{
    /**
     * Applies field filter to data row
     *
     * @param array $row
     * @param string $fieldName
     * @return bool
     */
    public function apply(array $row, string $fieldName): bool;
}
