<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Store;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterInterface;
use Amasty\ExportCore\Export\Filter\Utils\AfterFilterApplier;
use Magento\Framework\Data\Collection;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;

class Filter implements FilterInterface
{
    public const TYPE_ID = 'store';

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var AfterFilterApplier
     */
    private $afterFilterApplier;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        AfterFilterApplier $afterFilterApplier
    ) {
        $this->storeRepository = $storeRepository;
        $this->afterFilterApplier = $afterFilterApplier;
    }

    public function apply(Collection $collection, FieldFilterInterface $filter)
    {
        $config = $filter->getExtensionAttributes()->getStoreFilter();
        if (!$config) {
            return;
        }
        $condition = $this->prepareCondition($filter, $config);

        $collection->addFieldToFilter($filter->getField(), $condition);
    }

    public function applyAfter(array $row, FieldFilterInterface $filter): bool
    {
        $value = $row[$filter->getField()] ?? null;
        $config = $filter->getExtensionAttributes()->getStoreFilter();
        if (!$config || !$value) {
            return false;
        }
        $condition = $this->prepareCondition($filter, $config);

        return $this->afterFilterApplier->apply($condition, $value, null);
    }

    private function getStoreIds(): array
    {
        $storeIds = [];
        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            if ($store->getId() != Store::DEFAULT_STORE_ID) {
                $storeIds[] = $store->getId();
            }
        }

        return $storeIds;
    }

    private function prepareCondition(FieldFilterInterface $filter, ConfigInterface $config): array
    {
        $condition = [$filter->getCondition() => $config->getValue()];
        if (!empty($config->getValue()) && in_array(Store::DEFAULT_STORE_ID, $config->getValue())) {
            $condition = [$filter->getCondition() => $this->getStoreIds()];
        }

        return $condition;
    }
}
