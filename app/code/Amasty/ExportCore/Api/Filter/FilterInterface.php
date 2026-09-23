<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\Filter;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Magento\Framework\Data\Collection;

interface FilterInterface
{
    /**
     * Applies filtration before modifiers
     *
     * @param Collection $collection
     * @param FieldFilterInterface $filter
     *
     * @return void
     */
    public function apply(Collection $collection, FieldFilterInterface $filter);

    /**
     * Applies filtration after modifiers
     *
     * @param array $row
     * @param FieldFilterInterface $filter
     *
     * @return bool returns false if row is invalid
     */
    public function applyAfter(array $row, FieldFilterInterface $filter): bool;
}
