<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Modifier;

interface RowModifierInterface
{
    /**
     * @param array &$row
     * @return mixed
     */
    public function transform(array &$row): array;
}
