<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Config\Entity;

interface RelationEntityCodeResolverInterface
{
    public function resolve(string $fieldsConfigName, string $parentEntityCode): ?string;
}
