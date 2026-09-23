<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\ThirdParty;

use Magento\AdminGws\Model\Role;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;

class GwsAdapter
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    public function isEnabled(): bool
    {
        return class_exists(Role::class) && $this->moduleManager->isEnabled('Magento_AdminGws');
    }

    // @phpstan-ignore-next-line
    public function getRole(): ?Role
    {
        if ($this->isEnabled()) {
            return $this->objectManager->get(Role::class); //// @phpstan-ignore class.notFound
        }

        return null;
    }
}
