<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Config\Source;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\OptionSource\ImageStatus;

class Image extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Amasty\GiftCard\Model\Image\ResourceModel\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Amasty\GiftCard\Model\Image\ResourceModel\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->collectionFactory->create()
            ->showOnlyAdminUpload()
            ->addFieldToFilter(ImageInterface::STATUS, ImageStatus::ENABLED)
            ->toOptionArray();
    }
}
