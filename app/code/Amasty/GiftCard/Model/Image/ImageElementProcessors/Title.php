<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Api\ImageRepositoryInterface;
use Amasty\GiftCard\Model\Image\Utils\ImageElementCssMerger;

class Title implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        ImageRepositoryInterface $imageRepository
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->imageRepository = $imageRepository;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        $imageModel = $this->imageRepository->getById($imageElement->getImageId());
        if ($imageModel->getGcardTitle()) {
            return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
                . $imageModel->getGcardTitle()
                . '</span>';
        }

        return '';
    }

    public function getDefaultValue(): string
    {
        return __('Gift Card')->render();
    }
}
