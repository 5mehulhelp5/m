<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Framework\App\ResourceConnection;

class StoreId implements FieldValidatorInterface
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var array
     */
    private $validationResult = [];

    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $storeId = trim((string)$row[$field]);

            if (!empty($storeId)) {
                if (!isset($this->validationResult[$storeId])) {
                    $this->validationResult[$storeId] = $this->isStoreExists($storeId);
                }

                return $this->validationResult[$storeId];
            }
        }

        return true;
    }

    private function isStoreExists($storeId): bool
    {
        $storeTable = $this->connection->getTableName('store');
        $connection = $this->connection->getConnection();

        return (bool)$connection->fetchOne(
            $connection->select()
                ->from($storeTable)
                ->where('store_id = ?', trim($storeId))
                ->limit(1)
                ->columns(['store_id'])
        );
    }
}
