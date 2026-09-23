<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Modifier;

interface RelationModifierInterface
{
    /**
     * @param array &$entityRow
     * @param array &$subEntityRows
     * @return array
     */
    public function transform(array &$entityRow, array &$subEntityRows): array;
}
