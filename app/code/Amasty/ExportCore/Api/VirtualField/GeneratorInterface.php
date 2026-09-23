<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\VirtualField;

interface GeneratorInterface
{
    /**
     * @param array $currentRecord
     * @return mixed
     */
    public function generateValue(array $currentRecord);
}
