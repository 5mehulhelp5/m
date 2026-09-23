<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Utils;

use Amasty\ExportCore\Api\Config\Entity\Field\FilterInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterfaceFactory;
use Amasty\ExportCore\Api\Config\Profile\FieldsConfigInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\Filter\Type\Store\Filter;
use Amasty\ExportCore\Export\Filter\Type\Store\MetaFactory;
use Amasty\ImportExportCore\Config\ConfigClass\Factory;

/**
 * Adds store filters to fields config in case of Gws module restrictions
 */
class RestrictedStoresFiltersApplier
{
    /**
     * @var FieldFilterInterfaceFactory
     */
    private $filterInterfaceFactory;

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var Factory
     */
    private $configClassFactory;

    /**
     * @var string|null
     */
    private $storeIds;

    public function __construct(
        FieldFilterInterfaceFactory $filterInterfaceFactory,
        EntityConfigProvider $entityConfigProvider,
        Factory $configClassFactory
    ) {
        $this->filterInterfaceFactory = $filterInterfaceFactory;
        $this->entityConfigProvider = $entityConfigProvider;
        $this->configClassFactory = $configClassFactory;
    }

    /**
     * Must set manually 'cause no access to ProfileConfig
     * in \Amasty\ExportCore\Api\Config\Entity\SubEntityCollectorInterface
     */
    public function setStoreIds(string $storeIds): void
    {
        $this->storeIds = $storeIds;
    }

    public function apply(string $entityCode, FieldsConfigInterface $fieldsConfig): void
    {
        if (!$this->storeIds) {
            return;
        }

        $filters = $this->getExistingFiltersByName($fieldsConfig);
        foreach ($this->getEntityFieldStoreFilters($entityCode) as $field => $storeFilter) {
            if (!isset($filters[$field])) {
                $filters[$field] = $this->createFieldFilter(
                    $field,
                    $storeFilter,
                    $this->storeIds
                );
            }
        }
        $fieldsConfig->setFilters(array_values($filters));
    }

    private function getEntityFieldStoreFilters(string $entityCode): array
    {
        if (!($fieldsConfig = $this->entityConfigProvider->get($entityCode)->getFieldsConfig())) {
            return [];
        }
        $entityFields = $fieldsConfig->getFields();
        $entityFieldFilters = [];
        foreach ($entityFields as $field) {
            if ($field->getFilter() && $field->getFilter()->getType() === Filter::TYPE_ID) {
                $entityFieldFilters[$field->getName()] = $field->getFilter();
            }
        }

        return $entityFieldFilters;
    }

    private function getExistingFiltersByName(FieldsConfigInterface $fieldsConfig): array
    {
        $filters = [];
        foreach ((array)$fieldsConfig->getFilters() as $filter) {
            $filters[$filter->getField()] = $filter;
        }

        return $filters;
    }

    private function createFieldFilter(
        string $field,
        FilterInterface $filterConfig,
        string $storeIds
    ): FieldFilterInterface {
        $filter = $this->filterInterfaceFactory->create();
        $filter->setField($field);
        $filter->setCondition('in');
        $filter->setApplyAfterModifier(false);
        $this->configClassFactory
            ->createObject($filterConfig->getMetaClass())
            ->prepareConfig($filter, array_filter(explode(',', $storeIds)));

        return $filter;
    }
}
