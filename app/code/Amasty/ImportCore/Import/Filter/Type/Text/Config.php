<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filter\Type\Text;

class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $value;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): ConfigInterface
    {
        $this->value = $value;

        return $this;
    }
}
