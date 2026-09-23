<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\SampleData;

use Amasty\ImportCore\Api\Config\Relation\RelationConfigInterface;
use Amasty\ImportCore\Api\Source\SourceConfigInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\Config\RelationConfigProvider;
use Amasty\ImportCore\Import\Utils\Type\Table\Row\Header\DataHandler;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Amasty\ImportExportCore\Utils\Type\Table\Row\Mapping\Processor as MappingProcessor;

class FileContent
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SourceConfigInterface
     */
    private $sourceConfig;

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var RelationConfigProvider
     */
    private $relationConfigProvider;

    /**
     * @var MappingProcessor
     */
    private $mappingProcessor;

    /**
     * @var DataHandler
     */
    private $headerDataHandler;

    public function __construct(
        ObjectManagerInterface $objectManager,
        SourceConfigInterface $sourceConfig,
        EntityConfigProvider $entityConfigProvider,
        RelationConfigProvider $relationConfigProvider,
        MappingProcessor $mappingProcessor,
        DataHandler $headerDataHandler
    ) {
        $this->objectManager = $objectManager;
        $this->sourceConfig = $sourceConfig;
        $this->entityConfigProvider = $entityConfigProvider;
        $this->relationConfigProvider = $relationConfigProvider;
        $this->mappingProcessor = $mappingProcessor;
        $this->headerDataHandler = $headerDataHandler;
    }

    public function get(ProfileConfig $profileConfig): array
    {
        $entityCode = $profileConfig->getEntityCode();
        $source = $this->sourceConfig->get($profileConfig->getSourceType());
        $entity = $this->entityConfigProvider->get($entityCode);
        $relations = $this->relationConfigProvider->get($entityCode);

        if (empty($source['sampleFileGenerator'])) {
            throw new LocalizedException(__('Source doesn\'t have sample file generator.'));
        }

        if (empty($entity->getFieldsConfig()->getFields())) {
            throw new LocalizedException(__('Entity Fields Config is empty.'));
        }

        if (empty($entity->getFieldsConfig()->getSampleData())) {
            throw new LocalizedException(__('Entity doesn\'t have sample data.'));
        }

        $data = $this->prepareSampleData($entity->getFieldsConfig()->getSampleData());

        if (empty($data)) {
            throw new LocalizedException(__('Entity doesn\'t have sample data.'));
        }

        if (!empty($relations)) {
            foreach ($data as &$parentData) {
                $this->getSubEntitySampleData($relations, $parentData);
            }
        }

        $headerMap = $this->headerDataHandler->getHeaderMap($profileConfig->getEntitiesConfig(), $data[0]);
        $data = $this->mappingProcessor->process($data, $headerMap);

        $sampleFileGenerator = $this->objectManager->create($source['sampleFileGenerator']);
        $filename = $entityCode . '.' . $sampleFileGenerator->getExtension();
        $content = $sampleFileGenerator->generate($profileConfig, $data);

        return [$filename, $content];
    }

    private function getSubEntitySampleData(array $relations, array &$data, string $parentKey = '')
    {
        /** @var RelationConfigInterface $relation */
        foreach ($relations as $relation) {
            $entity = $this->entityConfigProvider->get($relation->getChildEntityCode());
            $preparedSampleData = $this->prepareSampleData(
                $entity->getFieldsConfig()->getSampleData() ?? []
            );
            if (!empty($data[$parentKey])) {
                foreach ($data[$parentKey] as &$parentData) {
                    $this->addSubEntitySampleData($relation, $parentData, $preparedSampleData);
                }
            } else {
                $this->addSubEntitySampleData($relation, $data, $preparedSampleData);
            }

            $childRelations = $relation->getRelations();
            if ($childRelations) {
                $this->getSubEntitySampleData($childRelations, $data, $relation->getSubEntityFieldName());
            }
        }
    }

    private function prepareSampleData(array $sampleData): array
    {
        $result = [];
        foreach ($sampleData as $row) {
            $preparedRow = [];
            foreach ($row->getValues() as $value) {
                $preparedRow[$value->getField()] = $value->getValue();
            }

            $result[] = $preparedRow;
        }

        return $result;
    }

    private function addSubEntitySampleData(
        RelationConfigInterface $relation,
        array &$parentData,
        array $childData
    ): void {
        $parentFieldName = $relation->getParentFieldName();
        $childFieldName = $relation->getChildFieldName();

        if (isset($parentData[$parentFieldName])) {
            $parentLinkValue = $parentData[$parentFieldName];

            /**
             * @param array $row
             * @return bool
             */
            $filterCallback = function (array $row) use ($parentLinkValue, $childFieldName) {
                return isset($row[$childFieldName])
                    && $row[$childFieldName] == $parentLinkValue;
            };

            $childSampleRows = array_filter($childData, $filterCallback);
            if (!empty($childSampleRows)) {
                $parentData[$relation->getSubEntityFieldName()] = $childSampleRows;
            }
        }
    }
}
