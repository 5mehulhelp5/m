<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Controller\Adminhtml;

abstract class AbstractCodePool extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Amasty_GiftCard::giftcard_code';
}
