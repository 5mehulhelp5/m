<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filter\Type\Date;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * @param string $value
     *
     * @return \Amasty\ImportCore\Import\Filter\Type\Date\ConfigInterface
     */
    public function setValue(?string $value): ConfigInterface;
}
