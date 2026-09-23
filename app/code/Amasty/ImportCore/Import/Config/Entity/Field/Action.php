<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Entity\Field;

use Amasty\ImportCore\Api\Config\Entity\Field\ActionInterface;
use Magento\Framework\DataObject;

class Action extends DataObject implements ActionInterface
{
    public const ACTION_CLASS = 'action_class';
    public const ACTION_GROUP = 'action_group';

    /**
     * @inheritDoc
     */
    public function getConfigClass()
    {
        return $this->getData(self::ACTION_CLASS);
    }

    /**
     * @inheritDoc
     */
    public function setConfigClass($configClass)
    {
        return $this->setData(self::ACTION_CLASS, $configClass);
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return $this->getData(self::ACTION_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setGroup($group)
    {
        return $this->setData(self::ACTION_GROUP, $group);
    }
}
