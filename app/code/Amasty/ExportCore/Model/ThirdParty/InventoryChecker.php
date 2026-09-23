<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\ThirdParty;

use Magento\Framework\Module\Manager;

class InventoryChecker
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function isEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }
}
