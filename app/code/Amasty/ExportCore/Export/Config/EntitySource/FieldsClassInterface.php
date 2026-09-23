<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Config\EntitySource;

interface FieldsClassInterface
{
    public function execute(
        \Amasty\ExportCore\Api\Config\Entity\FieldsConfigInterface $existingConfig,
        \Amasty\ExportCore\Export\Config\EntityConfig $entityConfig
    ): \Amasty\ExportCore\Api\Config\Entity\FieldsConfigInterface;
}
