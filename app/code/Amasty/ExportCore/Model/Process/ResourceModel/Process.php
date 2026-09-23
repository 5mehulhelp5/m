<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\Process\ResourceModel;

use Amasty\ExportCore\Model\Process\Process as ProcessModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Process extends AbstractDb
{
    public const TABLE_NAME = 'amasty_export_process';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ProcessModel::ID);
    }
}
