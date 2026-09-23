<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\ThirdParty;

use Magento\Framework\Module\Manager;

class SubscriptionFunctionalityChecker
{
    /**
     * @var Manager
     */
    private Manager $moduleManager;

    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function isEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_GiftCardSubscriptionFunctionality');
    }
}
