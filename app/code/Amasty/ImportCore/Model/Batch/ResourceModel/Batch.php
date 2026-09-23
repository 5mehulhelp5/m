<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Model\Batch\ResourceModel;

use Amasty\ImportCore\Model\Batch\Batch as BatchModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Batch extends AbstractDb
{
    public const TABLE_NAME = 'amasty_import_batch';

    /**
     * @var array
     */
    protected $_serializableFields = [
        BatchModel::BATCH_DATA => ['[]', []]
    ];

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, BatchModel::ID);
    }

    /**
     * @param string $processIdentity
     * @return int
     */
    public function countProcessBatches(string $processIdentity): int
    {
        $countSelect = $this->getConnection()->select()->from(
            $this->getMainTable(),
            [BatchModel::ID]
        )->where(
            BatchModel::PROCESS_IDENTITY . ' = "' . $processIdentity . '"'
        )->distinct();

        return count($this->getConnection()->fetchCol($countSelect));
    }

    /**
     * Delete all batches related to specified profile id and return number of deleted batches
     * @param string $processIdentity
     * @return int
     */
    public function deleteProcessData(string $processIdentity): int
    {
        return $this->getConnection()->delete(
            $this->getMainTable(),
            BatchModel::PROCESS_IDENTITY . ' = "' . $processIdentity . '"'
        );
    }
}
