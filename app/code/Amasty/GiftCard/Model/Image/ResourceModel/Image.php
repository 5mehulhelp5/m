<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\ResourceModel;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Image extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_image';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ImageInterface::IMAGE_ID);
    }
}
