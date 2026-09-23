<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Test\Integration\TestModule\Model\ResourceModel;

use Amasty\ExportCore\Test\Integration\TestModule\Model\TestEntity1 as TestEntity1Model;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TestEntity1 extends AbstractDb
{
    public const TABLE_NAME = 'amasty_export_test_entity1';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, TestEntity1Model::ID);
    }
}
