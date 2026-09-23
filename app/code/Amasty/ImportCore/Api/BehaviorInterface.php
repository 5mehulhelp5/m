<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;

/**
 * Import behavior interface
 */
interface BehaviorInterface
{
    /**
     * Performs import behavior
     *
     * @param array $data
     * @param string|null $customIdentifier Custom entity identifier
     * @return BehaviorResultInterface list of processed ids
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface;
}
