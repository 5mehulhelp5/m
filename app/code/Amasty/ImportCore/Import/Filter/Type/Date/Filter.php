<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filter\Type\Date;

use Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ImportCore\Import\Filter\AbstractFilter;
use Amasty\ImportCore\Import\Filter\FilterDataInterface;
use Amasty\ImportCore\Import\Filter\FilterDataInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Filter extends AbstractFilter
{
    public const TYPE_ID = 'date';

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        FilterDataInterfaceFactory $filterDataFactory,
        ConditionConverter $conditionConverter,
        DateTime $dateTime
    ) {
        parent::__construct($filterDataFactory);

        $this->conditionConverter = $conditionConverter;
        $this->dateTime = $dateTime;
    }

    protected function getFilterConfig(FieldFilterInterface $filter)
    {
        return $filter->getExtensionAttributes()->getDateFilter();
    }

    protected function prepareFilterData(FilterDataInterface $filterData)
    {
        $filterData->setValue($this->dateTime->gmtTimestamp($filterData->getValue()));
        $filterData->setFilterValue($this->dateTime->gmtTimestamp($filterData->getFilterValue()));
        $filterData->setCondition(
            $this->conditionConverter->convert(
                $filterData->getCondition(),
                $filterData->getFilterValue()
            )
        );
    }
}
