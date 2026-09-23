<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filter\Type\Select;

class Config implements ConfigInterface
{
    /**
     * @var array
     */
    private $value;

    /**
     * @var bool
     */
    private $isMultiselect;

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(?array $value): ConfigInterface
    {
        $this->value = $value;

        return $this;
    }

    public function getIsMultiselect(): ?bool
    {
        return $this->isMultiselect;
    }

    public function setIsMultiselect($isMultiselect): ConfigInterface
    {
        $this->isMultiselect = (bool)$isMultiselect;

        return $this;
    }
}
