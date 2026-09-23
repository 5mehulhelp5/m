<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Behavior;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\ColumnValueExpressionFactory;

class UniqueConstraintsProcessor
{
    private const INDEX_COL_NAME = 'Column_name';
    private const PRIMARY_FILTER = 'Key_name <> "PRIMARY"';
    private const UNIQUE_FILTER = 'Non_unique = 0';
    private const EXIST_RECORDS_KEY = 'exists_record';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ColumnValueExpressionFactory
     */
    private $columnValueExpressionFactory;

    /**
     * @var array [tableName => [constraints]]
     */
    private $uniqueConstraintsCache;

    public function __construct(
        ResourceConnection $resourceConnection,
        ColumnValueExpressionFactory $columnValueExpressionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->columnValueExpressionFactory = $columnValueExpressionFactory;
    }

    /**
     * Updates import data with PK field if it contains all necessary data to match it from constraints
     */
    public function updateData(array &$data, string $tableName, string $idFieldName): void
    {
        $uniqueFields = $this->getTableUniqueConstraintsFields($tableName);
        if (empty($uniqueFields)) {
            return;
        }

        $selectQuery = $this->prepareColumnsQuery($data, $uniqueFields);
        if (!$selectQuery) {
            return;
        }

        $existsRecords = $this->fetchExistsRecords($selectQuery, $tableName, $idFieldName);
        foreach ($existsRecords as $rowIndex => $idField) {
            $data[$rowIndex][$idFieldName] = $idField;
        }
    }

    private function getTableUniqueConstraintsFields(string $tableName): array
    {
        if (isset($this->uniqueConstraintsCache[$tableName])) {
            return $this->uniqueConstraintsCache[$tableName];
        }

        $constraintFields = [];
        $indexes = $this->resourceConnection->getConnection()->query(
            'SHOW INDEX FROM ' . $tableName . ' WHERE ' . self::PRIMARY_FILTER . ' AND ' . self::UNIQUE_FILTER
        )->fetchAll();

        // No matter how many unique constraints is in the table.
        // We can insert a row in it only if all fields from all unique constraints are not met in such combination
        // as in the row. So, we don't need to separate fields by their constraints and check it separately
        foreach ($indexes as $index) {
            $constraintFields[] = $index[self::INDEX_COL_NAME];
        }

        return $this->uniqueConstraintsCache[$tableName] = array_unique($constraintFields);
    }

    private function prepareColumnsQuery(array $data, array $uniqueFields): string
    {
        $selectQuery = '';
        foreach ($data as $index => $row) {
            $whenParts = [];
            foreach ($uniqueFields as $field) {
                if (isset($row[$field])) {
                    $whenParts[] = '`' . $field . '` = "' . $row[$field] . '"';
                } else {
                    //import row doesn't contain all unique fields values,
                    //so we can't select only one row from DB
                    continue 2;
                }
            }
            $selectQuery .= 'WHEN ' . implode(' AND ', $whenParts) . ' THEN ' . $index  . ' ';
        }

        return $selectQuery;
    }

    private function fetchExistsRecords(string $selectQuery, string $tableName, string $idFieldName): array
    {
        $selectQuery = $this->columnValueExpressionFactory->create([
            'expression' => '(CASE ' . $selectQuery . 'END) as ' . self::EXIST_RECORDS_KEY
        ]);
        $connection = $this->resourceConnection->getConnection();
        $query = $connection->select()->from(
            $tableName,
            [$selectQuery, $idFieldName]
        )->group(
            $idFieldName
        )->having(
            new \Zend_Db_Expr(self::EXIST_RECORDS_KEY . ' IS NOT NULL')
        );

        return $connection->fetchPairs($query);
    }
}
