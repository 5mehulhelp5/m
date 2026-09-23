<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Behavior\AddUpdate;

use Amasty\ImportCore\Api\Behavior\BehaviorObserverInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Api\BehaviorInterface;
use Amasty\ImportCore\Import\Behavior\Table as TableBehavior;
use Amasty\ImportCore\Import\Behavior\UniqueConstraintsProcessor;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Table extends TableBehavior implements BehaviorInterface
{
    /**
     * @var UniqueConstraintsProcessor
     */
    private $uniqueConstraintsProcessor;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        SerializerInterface $serializer,
        BehaviorResultInterfaceFactory $behaviorResultFactory,
        DuplicateFieldChecker $duplicateFieldChecker,
        UniqueConstraintsProcessor $uniqueConstraintsProcessor,
        array $config
    ) {
        parent::__construct(
            $objectManager,
            $resourceConnection,
            $serializer,
            $behaviorResultFactory,
            $duplicateFieldChecker,
            $config
        );
        $this->uniqueConstraintsProcessor = $uniqueConstraintsProcessor;
    }

    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        $preparedData = $this->prepareData($data);

        if (!$this->hasDataToInsert($preparedData)) {
            return $result;
        }

        if ($customIdentifier) {
            $this->updateDataIdFields($preparedData, $customIdentifier);
        }
        $this->serializeArrays($preparedData);
        $uniqueIds = $this->getUniqueIds($preparedData);
        $existingIds = $this->getExistingIds($uniqueIds);
        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $this->dispatchBehaviorEvent(
                BehaviorObserverInterface::BEFORE_APPLY,
                $preparedData
            );

            $maxId = $this->getMaxId();
            [$dataToInsert, $dataToUpdate] = $this->separateData($preparedData);
            if ($dataToInsert) {
                $connection->insertMultiple($this->getTable(), $dataToInsert);
            }
            if ($dataToUpdate) {
                $connection->insertOnDuplicate($this->getTable(), $dataToUpdate);
            }
            $newIds = $this->getNewIds($maxId);
            $uniqueIds = array_merge($uniqueIds, $newIds);

            $this->fillDataIds($data, $preparedData, $newIds);
            $result->setUpdatedIds(array_intersect($uniqueIds, $existingIds));
            $result->setNewIds(array_diff($uniqueIds, $existingIds));
            $result->setAffectedIds(array_intersect($this->getUniqueIds($data), $uniqueIds));

            $this->dispatchBehaviorEvent(
                BehaviorObserverInterface::AFTER_APPLY,
                $data
            );

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $result;
    }

    protected function getExistingIds(array $filledIds)
    {
        $select = $this->resourceConnection->getConnection()->select()
            ->from($this->getTable(), [$this->getIdField()])
            ->where($this->getIdField() . ' IN (?)', $filledIds);

        return $this->resourceConnection->getConnection()->fetchCol($select);
    }

    private function separateData(array $preparedData): array
    {
        $dataToInsert = $dataToUpdate = [];
        $this->uniqueConstraintsProcessor->updateData($preparedData, $this->getTable(), $this->getIdField());
        foreach ($preparedData as $row) {
            if (!empty($row[$this->getIdField()])) {
                $dataToUpdate[] = $row;
            } else {
                $dataToInsert[] = $row;
            }
        }

        return [$dataToInsert, $dataToUpdate];
    }

    private function fillDataIds(array &$data, array &$preparedData, $newIds): void
    {
        foreach ($preparedData as $index => $row) {
            if (!empty($row[$this->getIdField()])) {
                $data[$index][$this->getIdField()] = $row[$this->getIdField()];
            } else {
                $data[$index][$this->getIdField()] = array_shift($newIds);
            }
        }
    }
}
