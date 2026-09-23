<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\EntitySource\Xml;

use Amasty\ImportCore\Api\Config\Entity\FieldsConfigInterface;

interface FieldsClassInterface
{
    public function execute(FieldsConfigInterface $existingConfig): FieldsConfigInterface;
}
