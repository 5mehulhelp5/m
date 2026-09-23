<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Integration\TestModule\Model;

use Magento\Framework\Model\AbstractModel;

class TestEntity1 extends AbstractModel
{
    public const ID = 'id';
    public const FIELD_1 = 'field_1';
    public const FIELD_2 = 'field_2';
    public const FIELD_3 = 'field_3';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\TestEntity1::class);
        $this->setIdFieldName(self::ID);
    }
}
