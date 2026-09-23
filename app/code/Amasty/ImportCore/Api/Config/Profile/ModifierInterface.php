<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Profile;

/**
 * Profile field modifier
 */
interface ModifierInterface
{
    /**
     * @return string
     */
    public function getModifierClass(): string;

    /**
     * @param string $modifierClass
     * @return void
     */
    public function setModifierClass(string $modifierClass);

    /**
     * @return string
     */
    public function getGroup(): string;

    /**
     * @param string $group
     * @return void
     */
    public function setGroup(string $group): void;

    /**
     * @return \Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface[]|null
     */
    public function getArguments(): ?array;

    /**
     * @param \Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface[]|null $arguments
     * @return void
     */
    public function setArguments(?array $arguments): void;
}
