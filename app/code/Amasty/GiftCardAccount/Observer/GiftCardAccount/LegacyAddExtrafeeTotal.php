<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Observer\GiftCardAccount;

use Amasty\Base\Model\ModuleInfoProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager;

/**
 * @deprecated used for backward compatibility only
 * all edits must be applied in Amasty_Extrafee module observer
 * @see \Amasty\Extrafee\Observer\ModifyGiftCardAllowedSubtotal
 */
class LegacyAddExtrafeeTotal implements ObserverInterface
{
    private const MODULE_NAME = 'Amasty_Extrafee';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        ModuleInfoProvider $moduleInfoProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    public function execute(Observer $observer)
    {
        if (!$this->canExecute()) {
            return;
        }
        if (!$this->scopeConfig->isSetFlag('amgiftcard/general/allow_to_paid_for_amasty_extra_fee')) {
            return;
        }
        $event = $observer->getEvent();
        $fromObject = $event->getData('from_object');
        $value = $event->getData('value');

        $value += (float)$fromObject->getAmastyExtrafeeAmount();
        $event->setData('value', $value);
    }

    private function canExecute(): bool
    {
        if (!$this->moduleManager->isEnabled(self::MODULE_NAME)) {
            return false;
        }

        $incompatibleVersion = '1.6.7';
        $originalVersion = $this->moduleInfoProvider->getModuleInfo(self::MODULE_NAME)['version'] ?? '0.0.1';

        return version_compare($incompatibleVersion, $originalVersion) > 0;
    }
}
