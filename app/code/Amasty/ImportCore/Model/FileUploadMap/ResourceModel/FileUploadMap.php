<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Model\FileUploadMap\ResourceModel;

use Amasty\ImportCore\Model\FileUploadMap\FileUploadMap as FileUploadMapModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FileUploadMap extends AbstractDb
{
    public const TABLE_NAME = 'amasty_import_file_upload_map';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, FileUploadMapModel::ID);
    }
}
