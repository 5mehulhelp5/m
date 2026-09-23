<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

use Amasty\GiftCard\Model\CodePool\CodePool;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var CodePool $codePool */
$codePool = $objectManager->create(CodePool::class)->load('test code pool', 'title');

try {
    $objectManager->create(\Amasty\GiftCard\Api\CodePoolRepositoryInterface::class)->delete($codePool);
} catch (\Exception $e) {
    null;
}
