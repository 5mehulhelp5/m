<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\Process\ResourceModel;

use Amasty\ExportCore\Model\Process\Process;
use Amasty\ExportCore\Model\Process\ResourceModel\Process as ProcessResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Process::class, ProcessResource::class);
    }
}
