<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Action\Preparation\Collection;

use Amasty\ExportCore\Api\Config\Profile\FieldsConfigInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\Config\RelationConfigProvider;
use Amasty\ExportCore\Export\Filter\EntityFiltersProvider;
use Amasty\ExportCore\Export\Filter\Utils\RestrictedStoresFiltersApplier;
use Amasty\ExportCore\Export\SubEntity\CollectorFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Data\Collection;

class PrepareCollection
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var CollectorFactory
     */
    private $collectorFactory;

    /**
     * @var RelationConfigProvider
     */
    private $relationConfigProvider;

    /**
     * @var EntityFiltersProvider
     */
    private $entityFiltersProvider;

    /**
     * @var RestrictedStoresFiltersApplier
     */
    private $restrictedStoresFiltersApplier;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        RelationConfigProvider $relationConfigProvider,
        CollectorFactory $collectorFactory,
        EntityFiltersProvider $entityFiltersProvider,
        RestrictedStoresFiltersApplier $restrictedStoresFiltersApplier
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->collectorFactory = $collectorFactory;
        $this->relationConfigProvider = $relationConfigProvider;
        $this->entityFiltersProvider = $entityFiltersProvider;
        $this->restrictedStoresFiltersApplier = $restrictedStoresFiltersApplier
            ?? ObjectManager::getInstance()->get(RestrictedStoresFiltersApplier::class);
    }

    public function execute(Collection $collection, string $entityCode, FieldsConfigInterface $fieldsConfig)
    {
        if ($collection instanceof SourceProviderInterface) {
            $this->addFieldsToSelect($collection, $entityCode, $fieldsConfig->getFields());
            $this->addSubentityRequiredFields($collection, $entityCode);
        }
        $this->restrictedStoresFiltersApplier->apply($entityCode, $fieldsConfig);
        $this->applyFilters($collection, $entityCode, $fieldsConfig);
    }

    public function addFieldsToSelect(Collection $collection, string $entityCode, ?array $fields): PrepareCollection
    {
        $fieldsToSelect = [];
        if (!empty($fields)) {
            $entityFields = $this->entityConfigProvider->get($entityCode)->getFieldsConfig()->getFields();
            $entityFieldNames = [];
            foreach ($entityFields as $entityField) {
                $entityFieldNames[] = $entityField->getName();
            }
            foreach ($fields as $field) {
                if (in_array($field->getName(), $entityFieldNames)) {
                    $fieldsToSelect[] = $field->getName();
                }
            }
        }

        if (!empty($fieldsToSelect)) {
            $collection->addFieldToSelect($fieldsToSelect);
        }

        return $this;
    }

    public function applyFilters(
        Collection $collection,
        string $entityCode,
        FieldsConfigInterface $fieldsConfig
    ): PrepareCollection {
        if (empty($fieldsConfig->getFilters())) {
            return $this;
        }

        $filters = $this->entityFiltersProvider->get($entityCode, $fieldsConfig, false);
        foreach ($filters as $filterConfig) {
            $filterConfig[EntityFiltersProvider::KEY_FILTER_INSTANCE]->apply(
                $collection,
                $filterConfig[EntityFiltersProvider::KEY_FIELD_FILTER]
            );
        }

        return $this;
    }

    protected function addSubentityRequiredFields(Collection $collection, string $entityCode): PrepareCollection
    {
        $relations = $this->relationConfigProvider->get($entityCode);
        if (!empty($relations)) {
            foreach ($relations as $relationConfig) {
                foreach ($this->collectorFactory->create($relationConfig)->getParentRequiredFields() as $field) {
                    $collection->addFieldToSelect($field);
                }
            }
        }

        return $this;
    }
}
