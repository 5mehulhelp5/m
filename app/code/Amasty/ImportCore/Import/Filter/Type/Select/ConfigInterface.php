<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filter\Type\Select;

interface ConfigInterface
{
    /**
     * @return string[]|null
     */
    public function getValue(): ?array;

    /**
     * @param string[] $value
     *
     * @return \Amasty\ImportCore\Import\Filter\Type\Select\ConfigInterface
     */
    public function setValue(?array $value): ConfigInterface;

    /**
     * @return bool|null
     */
    public function getIsMultiselect(): ?bool;

    /**
     * @param bool $isMultiselect
     *
     * @return \Amasty\ImportCore\Import\Filter\Type\Select\ConfigInterface
     */
    public function setIsMultiselect($isMultiselect): ConfigInterface;
}
