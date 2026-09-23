<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Behavior;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Api\BehaviorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;

class TableWithCompositePKey implements BehaviorInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var BehaviorResultInterfaceFactory
     */
    private $behaviorResultFactory;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var array
     */
    private $config;

    public function __construct(
        ResourceConnection $resourceConnection,
        BehaviorResultInterfaceFactory $behaviorResultFactory,
        MetadataPool $metadataPool,
        array $config
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->behaviorResultFactory = $behaviorResultFactory;
        $this->metadataPool = $metadataPool;
        $this->config = $config;

        if (!isset($this->config['tableName'])) {
            throw new \RuntimeException('tableName isn\'t specified.');
        }

        if (!isset($this->config['primaryKeyFields'])) {
            throw new \RuntimeException('primaryKeyFields isn\'t specified.');
        }
    }

    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->behaviorResultFactory->create();
        if (empty($data)) {
            return $result;
        }

        list($toInsert, $toUpdate) = $this->prepareDataForSave($data);
        $connection = $this->getConnection();
        $mainTable = $this->getTableName($this->getMainTable());
        $identityKey = $this->getIdentityKey();

        $deleteIds = $this->deleteExistingUnusedData(
            $this->prepareDataForTable($data, $mainTable),
            $mainTable,
            $identityKey
        );

        if ($toInsert) {
            $newIds = array_column($toInsert, $identityKey);
            $deleteIds = array_diff($deleteIds, $newIds);
            $connection->insertOnDuplicate(
                $mainTable,
                $this->prepareDataForTable($toInsert, $mainTable)
            );
            $result->setNewIds($newIds);
        }

        if ($toUpdate) {
            $updatedIds = array_column($toUpdate, $identityKey);
            $deleteIds = array_diff($deleteIds, $updatedIds);
            $connection->insertOnDuplicate(
                $mainTable,
                $this->prepareDataForTable($toUpdate, $mainTable)
            );
            $result->setUpdatedIds($updatedIds);
        }

        $result->setDeletedIds($deleteIds);

        return $result;
    }

    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }

    private function prepareDataForTable(array $data, string $tableName): array
    {
        if (empty($data)) {
            return $data;
        }

        $columns = $this->getConnection()->describeTable($tableName);
        $columnsToUnset = current($data) ? array_keys(current($data)) : [];
        foreach ($columns as $column => $value) {
            if (false !== $key = array_search($column, $columnsToUnset)) {
                unset($columnsToUnset[$key]);
            }
        }

        if (!empty($columnsToUnset)) {
            $data = $this->unsetColumns($data, $columnsToUnset);
        }

        return $data;
    }

    private function unsetColumns(array $data, array $columns): array
    {
        foreach ($data as &$row) {
            foreach ($columns as $column) {
                unset($row[$column]);
            }
        }

        return $data;
    }

    private function getTableName(string $table): string
    {
        return $this->resourceConnection->getTableName($table);
    }

    private function prepareDataForSave(array $data): array
    {
        $toInsert = $toUpdate = [];
        $tableName = $this->getTableName($this->getMainTable());
        $pkFields = $this->getValidPrimaryKeyFields($this->getPrimaryKeyFields(), $tableName);
        $pkHash = $this->getPKhash($data, $tableName, $pkFields);

        foreach ($data as $row) {
            if (!$this->isExistPK($row, $pkFields, $pkHash)) {
                $toInsert[] = $row;
            } else {
                $toUpdate[] = $row;
            }
        }

        return [$toInsert, $toUpdate];
    }

    private function getPKhash(array $data, string $tableName, array $pkFields): array
    {
        $pkHash = [];
        $conditions = [];
        $connection = $this->getConnection();

        foreach ($pkFields as $pkField) {
            if ($pkFieldValues = array_unique(array_column($data, $pkField))) {
                $conditions[] = $connection->quoteInto($pkField . ' IN(?)', $pkFieldValues);
            }
        }

        $select = $connection->select()->from(
            $tableName,
            $pkFields
        )->where(implode(' ' . Select::SQL_AND . ' ', $conditions));

        $existPKdata = $connection->fetchAll($select);
        foreach ($existPKdata as $row) {
            $pkHash[$this->buildHash($row, $pkFields)] = true;
        }

        return $pkHash;
    }

    private function buildHash(array $row, array $pkFields): string
    {
        $cacheKeyParts = [];

        foreach ($pkFields as $pkField) {
            if (isset($row[$pkField])) {
                $cacheKeyParts[] = $pkField . '-' . $row[$pkField];
            }
        }

        return implode('-', $cacheKeyParts);
    }

    private function isExistPK(array $row, array $pkFields, array &$pkHash): bool
    {
        return isset($pkHash[$this->buildHash($row, $pkFields)]);
    }

    private function getValidPrimaryKeyFields(array $primaryKeyFields, string $tableName): array
    {
        $columns = $this->getConnection()->describeTable($tableName);

        return array_values(array_intersect($primaryKeyFields, array_keys($columns)));
    }

    private function deleteExistingUnusedData(array $data, string $tableName, string $identityKey): array
    {
        $conditions = [];
        $connection = $this->getConnection();
        $pkFields = $this->getValidPrimaryKeyFields($this->getPrimaryKeyFields(), $tableName);

        foreach ($pkFields as $pkField) {
            $pkFieldValues = array_unique(array_column($data, $pkField));
            if (empty($pkFieldValues)) {
                return [];
            }

            $conditions[] = $connection->quoteInto(
                $pkField . ($pkField != $identityKey ? ' NOT' : '') . ' IN(?)',
                $pkFieldValues
            );
        }
        $condition = implode(' ' . Select::SQL_AND . ' ', $conditions);

        $select = $connection->select()
            ->from($tableName, [$identityKey])
            ->where($condition)
            ->distinct(true);
        $deleteIds = $connection->fetchCol($select);

        if ($deleteIds) {
            $connection->delete($tableName, $condition);
        }

        return $deleteIds;
    }

    private function getIdentityKey(): string
    {
        if (isset($this->config['identityKey'])) {
            $identityKey = (string)$this->config['identityKey'];
        } elseif (isset($this->config['identityEntity'])) {
            $metadata = $this->metadataPool->getMetadata($this->config['identityEntity']);
            $identityKey = (string)$metadata->getLinkField();
        } else {
            $primaryKeyFields = $this->getPrimaryKeyFields();
            $identityKey = (string)array_shift($primaryKeyFields);
        }

        return $identityKey;
    }

    private function getPrimaryKeyFields(): array
    {
        $primaryKeyFields = (array)$this->config['primaryKeyFields'];

        if (isset($this->config['identityEntity'])) {
            $primaryKeyFields[] = $this->getIdentityKey();
        }

        return $primaryKeyFields;
    }

    private function getMainTable(): string
    {
        return (string)$this->config['tableName'];
    }
}
