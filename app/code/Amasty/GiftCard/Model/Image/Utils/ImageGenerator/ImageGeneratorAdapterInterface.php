<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\Utils\ImageGenerator;

interface ImageGeneratorAdapterInterface
{
    public function initImage(string $imageBlob, int $width, int $height): void;

    public function writeImage(string $filepath): void;
}
