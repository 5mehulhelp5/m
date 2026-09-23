<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Date;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterInterface;
use Amasty\ExportCore\Export\Filter\Utils\AfterFilterApplier;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Filter implements FilterInterface
{
    public const TYPE_ID = 'date';

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var AfterFilterApplier
     */
    private $afterFilterApplier;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var AfterFilterConditionConverter
     */
    private $afterFilterConditionConverter;

    public function __construct(
        ConditionConverter $conditionConverter,
        AfterFilterApplier $afterFilterApplier,
        DateTime $dateTime,
        ?AfterFilterConditionConverter $afterFilterConditionConverter = null
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->afterFilterApplier = $afterFilterApplier;
        $this->dateTime = $dateTime;
        $this->afterFilterConditionConverter =
            $afterFilterConditionConverter ?? ObjectManager::getInstance()->get(AfterFilterConditionConverter::class);
    }

    public function apply(Collection $collection, FieldFilterInterface $filter)
    {
        $config = $filter->getExtensionAttributes()->getDateFilter();
        if (!$config) {
            return;
        }

        $condition = $this->conditionConverter->convert(
            $filter->getCondition(),
            $config->getValue()
        );
        $collection->addFieldToFilter($filter->getField(), $condition);
    }

    public function applyAfter(array $row, FieldFilterInterface $filter): bool
    {
        $value = $row[$filter->getField()] ?? null;
        $config = $filter->getExtensionAttributes()->getDateFilter();
        if (!$config || !$value) {
            return false;
        }
        if (!($value = $this->dateTime->gmtTimestamp($value))) {
            return false;
        }

        $condition = $this->afterFilterConditionConverter->convert(
            $filter->getCondition(),
            $config->getValue()
        );

        return $this->afterFilterApplier->apply($condition, $value, null);
    }
}
