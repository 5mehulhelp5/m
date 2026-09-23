<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Model\Process\ResourceModel;

use Amasty\ImportCore\Model\Process\Process as ProcessModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Process extends AbstractDb
{
    public const TABLE_NAME = 'amasty_import_process';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ProcessModel::ID);
    }
}
