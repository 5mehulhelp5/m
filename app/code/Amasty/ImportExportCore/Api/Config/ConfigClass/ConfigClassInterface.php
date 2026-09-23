<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Config\ConfigClass;

interface ConfigClassInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return \Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface[]
     */
    public function getArguments(): ?array;
}
