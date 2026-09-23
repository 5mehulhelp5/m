<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Text;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterInterface;
use Amasty\ExportCore\Export\Filter\Utils\AfterFilterApplier;
use Magento\Framework\Data\Collection;

class Filter implements FilterInterface
{
    public const TYPE_ID = 'text';

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
        $config = $filter->getExtensionAttributes()->getTextFilter();
        if (!$config) {
            return;
        }

        $value = $config->getValue();
        switch ($filter->getCondition()) {
            case 'like':
            case 'nlike':
                $value = '%' . $value . '%';
                break;
            case 'in':
            case 'nin':
                $value = explode(PHP_EOL, $value);
                break;
        }

        $collection->addFieldToFilter(
            $filter->getField(),
            [$filter->getCondition() => $value]
        );
    }

    public function applyAfter(array $row, FieldFilterInterface $filter): bool
    {
        $value = $row[$filter->getField()] ?? null;
        $config = $filter->getExtensionAttributes()->getTextFilter();
        if (!$config || !$value) {
            return false;
        }
        $configValue = $config->getValue();
        switch ($filter->getCondition()) {
            case 'in':
            case 'nin':
                $configValue = explode(PHP_EOL, $configValue);
                break;
        }

        return $this->afterFilterApplier->apply($filter->getCondition(), $value, $configValue);
    }
}
