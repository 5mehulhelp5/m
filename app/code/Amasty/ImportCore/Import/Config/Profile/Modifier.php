<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Profile;

use Amasty\ImportCore\Api\Config\Profile\ModifierInterface;
use Magento\Framework\DataObject;

class Modifier extends DataObject implements ModifierInterface
{
    public const MODIFIER_CLASS = 'modifier_class';
    public const MODIFIER_GROUP = 'modifier_group';
    public const ARGUMENTS = 'arguments';

    public function getModifierClass(): string
    {
        return $this->getData(self::MODIFIER_CLASS);
    }

    public function setModifierClass(string $modifierClass)
    {
        $this->setData(self::MODIFIER_CLASS, $modifierClass);
    }

    public function getGroup(): string
    {
        return $this->getData(self::MODIFIER_GROUP);
    }

    public function setGroup($group): void
    {
        $this->setData(self::MODIFIER_GROUP, $group);
    }

    public function getArguments(): ?array
    {
        return $this->getData(self::ARGUMENTS);
    }

    public function setArguments(?array $arguments): void
    {
        $this->setData(self::ARGUMENTS, $arguments);
    }
}
