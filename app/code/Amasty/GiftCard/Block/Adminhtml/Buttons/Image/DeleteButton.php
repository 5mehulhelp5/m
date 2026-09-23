<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Block\Adminhtml\Buttons\Image;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Block\Adminhtml\Buttons\AbstractDeleteButton;

class DeleteButton extends AbstractDeleteButton
{
    /**
     * @return string
     */
    protected function getIdField(): string
    {
        return ImageInterface::IMAGE_ID;
    }
}
