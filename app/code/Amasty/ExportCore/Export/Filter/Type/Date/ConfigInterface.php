<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Date;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * @param string $value
     *
     * @return \Amasty\ExportCore\Export\Filter\Type\Date\ConfigInterface
     */
    public function setValue(?string $value): ConfigInterface;
}
