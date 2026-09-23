<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filter\Type\Text;

use Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ImportCore\Import\Filter\AbstractFilter;
use Amasty\ImportCore\Import\Filter\FilterDataInterface;

class Filter extends AbstractFilter
{
    public const TYPE_ID = 'text';

    protected function getFilterConfig(FieldFilterInterface $filter)
    {
        return $filter->getExtensionAttributes()->getTextFilter();
    }

    protected function prepareFilterData(FilterDataInterface $filterData)
    {
        switch ($filterData->getCondition()) {
            case 'in':
            case 'nin':
                $filterData->setFilterValue(explode(PHP_EOL, (string)$filterData->getFilterValue()));
                break;
        }
    }
}
