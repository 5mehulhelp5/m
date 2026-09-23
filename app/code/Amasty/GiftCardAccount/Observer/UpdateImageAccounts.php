<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Observer;

use Amasty\GiftCard\Model\Image\Image;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account as AccountResource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdateImageAccounts implements ObserverInterface
{
    /**
     * @var AccountResource
     */
    private $accountResource;

    public function __construct(
        AccountResource $accountResource
    ) {
        $this->accountResource = $accountResource;
    }

    public function execute(Observer $observer)
    {
        /** @var Image $image */
        if (($image = $observer->getEvent()->getImage())) {
            $this->accountResource->markChangedAccountImages($image->getImageId());
        }
    }
}
