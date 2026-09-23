<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Behavior\Delete;

use Amasty\ImportCore\Api\Behavior\BehaviorObserverInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Api\BehaviorInterface;
use Amasty\ImportCore\Import\Behavior\Table as TableBehavior;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Table extends TableBehavior implements BehaviorInterface
{
    public function __construct(
        ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        SerializerInterface $serializer,
        BehaviorResultInterfaceFactory $behaviorResultFactory,
        DuplicateFieldChecker $duplicateFieldChecker,
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
        $this->idField = $config['deleteByField'] ?? null;
    }

    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();

        $ids = $this->getIdsForDelete($data, $customIdentifier);
        if ($ids) {
            $connection = $this->getConnection();
            $connection->beginTransaction();
            try {
                $this->dispatchBehaviorEvent(BehaviorObserverInterface::BEFORE_APPLY, $ids);
                $connection->delete(
                    $this->getTable(),
                    $connection->quoteInto($this->getIdField() . ' IN (?)', $ids)
                );
                $this->dispatchBehaviorEvent(BehaviorObserverInterface::AFTER_APPLY, $ids);

                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        }
        $result->setDeletedIds($ids);

        return $result;
    }

    private function getIdsForDelete(array &$data, ?string $customIdentifier = null): array
    {
        $preparedData = $data;
        if ($customIdentifier) {
            $this->updateDataIdFields($preparedData, $customIdentifier);
        }

        $uniqueIds = $this->getUniqueIds($preparedData);
        if (empty($uniqueIds)) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $idFieldName = $this->getIdField();

        $select = $connection->select()
            ->from($this->getTable(), [$idFieldName])
            ->where($idFieldName . ' IN (?)', $uniqueIds);

        return $connection->fetchCol($select);
    }
}
