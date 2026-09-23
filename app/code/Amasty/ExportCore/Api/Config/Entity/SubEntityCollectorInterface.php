<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\Config\Entity;

use Amasty\ExportCore\Api\Config\Profile\FieldsConfigInterface;

interface SubEntityCollectorInterface
{
    /**
     * @param array $parentData
     * @param FieldsConfigInterface $fieldsConfig
     *
     * @return \Amasty\ExportCore\Api\Config\Entity\SubEntityCollectorInterface
     */
    public function collect(array &$parentData, FieldsConfigInterface $fieldsConfig): SubEntityCollectorInterface;

    public function getParentRequiredFields(): array;
}
