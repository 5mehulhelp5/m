<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Gift Card Sms Notifications for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\GiftCardSmsNotifications\Model\Smspro;

use Magento\Framework\ObjectManagerInterface;

class ApicallFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $className = \Magecomp\Smspro\Helper\Apicall::class)
    {
        return $this->objectManager->create($className);
    }
}
