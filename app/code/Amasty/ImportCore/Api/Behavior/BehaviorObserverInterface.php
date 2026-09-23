<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Behavior;

/**
 * Observer of behavior events
 */
interface BehaviorObserverInterface
{
    /**#@+
     * Behavior event types
     */
    public const BEFORE_APPLY = 'beforeApply';
    public const AFTER_APPLY = 'afterApply';
    /**#@-*/

    /**
     * Execute behavior observer logic
     *
     * @param array $data
     * @return void
     */
    public function execute(array &$data): void;
}
