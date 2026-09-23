<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Filter;

use Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface;

/**
 * Filter of certain type
 */
interface FilterInterface
{
    /**
     * @param array $row
     * @param string $fieldName
     * @param FieldFilterInterface $filter
     * @return bool
     */
    public function filter(array $row, string $fieldName, FieldFilterInterface $filter): bool;
}
