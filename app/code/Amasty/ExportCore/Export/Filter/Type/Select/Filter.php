<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Select;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterInterface;
use Amasty\ExportCore\Export\Filter\Utils\AfterFilterApplier;
use Magento\Framework\Data\Collection;

class Filter implements FilterInterface
{
    public const TYPE_ID = 'select';

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
        $config = $filter->getExtensionAttributes()->getSelectFilter();
        if (!$config) {
            return;
        }

        $condition = $this->prepareCondition($filter, $config);

        $collection->addFieldToFilter($filter->getField(), $condition);
    }

    public function applyAfter(array $row, FieldFilterInterface $filter): bool
    {
        $value = $row[$filter->getField()] ?? null;
        $config = $filter->getExtensionAttributes()->getSelectFilter();
        if (!$config || !$value) {
            return false;
        }
        $condition = $this->prepareCondition($filter, $config);

        return $this->afterFilterApplier->apply($condition, $value, null);
    }

    private function prepareCondition(FieldFilterInterface $filter, ConfigInterface $config): array
    {
        $condition = [$filter->getCondition() => $config->getValue()];
        if ($config->getIsMultiselect()
            && in_array($filter->getCondition(), ['finset', 'nfinset'])
            && !empty($config->getValue())
        ) {
            $condition = [];
            foreach ($config->getValue() as $item) {
                $condition[] = [$filter->getCondition() => $item];
            }
        }

        return $condition;
    }
}
