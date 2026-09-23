<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter;

use Amasty\ExportCore\Api\Config\Entity\Field\FilterInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldsConfigInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ImportExportCore\Config\ConfigClass\Factory as ConfigClassFactory;

class EntityFiltersProvider
{
    /**
     * Keys for result filters array
     */
    public const KEY_FILTER_INSTANCE = 0;
    public const KEY_FIELD_FILTER = 1;

    /**
     * Keys for prepared filters storage
     */
    private const BEFORE_FILTER_KEY = 'before';
    private const AFTER_FILTER_KEY = 'after';

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var ConfigClassFactory
     */
    private $configClassFactory;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * Storage for entity prepared filters
     *
     * @var array
     */
    private $preparedFilters = [];

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        ConfigClassFactory $configClassFactory,
        FilterProvider $filterProvider
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->configClassFactory = $configClassFactory;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @param string $entityCode
     * @param FieldsConfigInterface $fieldsConfig
     * @param bool $isAfterFilters
     *
     * @return array [KEY_FILTER_INSTANCE, KEY_FIELD_FILTER]
     */
    public function get(
        string $entityCode,
        FieldsConfigInterface $fieldsConfig,
        bool $isAfterFilters = false
    ): array {
        if (empty($fieldsConfig->getFilters())) {
            return [];
        }
        $result = [];

        if (!isset($this->preparedFilters[$entityCode])) {
            $this->prepareEntityFilters($entityCode, $fieldsConfig);
        }
        $entityFieldsFilter = $this->prepareEntityFieldFilters($entityCode);
        $key = $isAfterFilters ? self::AFTER_FILTER_KEY : self::BEFORE_FILTER_KEY;
        foreach ($this->preparedFilters[$entityCode][$key] as $filter) {
            if (!empty($filter->getType())) {
                $filterInstance = $this->filterProvider->getFilter($filter->getType());
            } elseif (!empty($filter->getFilterClass())) {
                $filterInstance = $this->configClassFactory->createObject($filter->getFilterClass());
            } elseif (!empty($entityFieldsFilter[$filter->getField()])) {
                $filterInstance = $this->configClassFactory->createObject(
                    $entityFieldsFilter[$filter->getField()]->getFilterClass()
                );
            } else {
                continue;
            }
            $result[] = [self::KEY_FILTER_INSTANCE => $filterInstance, self::KEY_FIELD_FILTER => $filter];
        }

        return $result;
    }

    /**
     * @param string $entityCode
     *
     * @return FilterInterface[]
     */
    private function prepareEntityFieldFilters(string $entityCode): array
    {
        $entityFieldsFilter = [];

        $entityFields = $this->entityConfigProvider->get($entityCode)->getFieldsConfig()->getFields();
        foreach ($entityFields as $field) {
            $entityFieldsFilter[$field->getName()] = $field->getFilter();
        }

        return $entityFieldsFilter;
    }

    /**
     * @param string $entityCode
     * @param FieldsConfigInterface $fieldsConfig
     *
     * @return void
     */
    private function prepareEntityFilters(string $entityCode, FieldsConfigInterface $fieldsConfig): void
    {
        $beforeFilters = $afterFilters = [];

        $fields = $this->getFieldsByName($fieldsConfig->getFields());
        foreach ($fieldsConfig->getFilters() as $filter) {
            if (!$filter->getApplyAfterModifier()) {
                $beforeFilters[] = $filter;
            } else {
                if (!isset($fields[$filter->getField()]) //if field not configured
                    || empty($fields[$filter->getField()]->getModifiers()) //or doesn't have modifier
                ) {
                    $beforeFilters[] = $filter; //we can't apply filter after ('cause no row data to apply to)
                } else {
                    $afterFilters[] = $filter;
                }
            }
        }

        $this->preparedFilters[$entityCode] = [
            self::BEFORE_FILTER_KEY => $beforeFilters,
            self::AFTER_FILTER_KEY => $afterFilters
        ];
    }

    /**
     * @param FieldInterface[] $fields
     *
     * @return FieldInterface[]
     */
    private function getFieldsByName(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[$field->getName()] = $field;
        }

        return $result;
    }
}
