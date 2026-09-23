<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Toggle;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterInterface;
use Amasty\ExportCore\Export\Filter\Utils\AfterFilterApplier;
use Magento\Framework\Data\Collection;

class Filter implements FilterInterface
{
    public const TYPE_ID = 'toggle';

    /**
     * @var AfterFilterApplier
     */
    private $afterFilterApplier;

    public function __construct(
        AfterFilterApplier $afterFilterApplier
    ) {
        $this->afterFilterApplier = $afterFilterApplier;
    }

    public function apply(Collection $collection, FieldFilterInterface $filter)
    {
        $config = $filter->getExtensionAttributes()->getToggleFilter();
        if (!$config) {
            return;
        }
        $collection->addFieldToFilter(
            $filter->getField(),
            ['eq' => $config->getValue()]
        );
    }

    public function applyAfter(array $row, FieldFilterInterface $filter): bool
    {
        $value = $row[$filter->getField()] ?? null;
        $config = $filter->getExtensionAttributes()->getToggleFilter();
        if (!$config || !$value) {
            return true;
        }

        return $this->afterFilterApplier->apply('eq', $value, $config->getValue());
    }
}
