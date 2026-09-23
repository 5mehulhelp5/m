<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\ResourceModel;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class ImageElements extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_image_elements';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ImageElementsInterface::ELEMENT_ID);
    }
}
