<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Test\Integration\TestModule\Model\ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\ExportCore\Test\Integration\TestModule\Model\TestEntity1::class,
            TestEntity1::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
