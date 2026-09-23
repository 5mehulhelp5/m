<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;

interface ImageElementProcessorInterface
{
    public function generateHtml(ImageElementsInterface $imageElement): string;

    public function getDefaultValue(): string;
}
