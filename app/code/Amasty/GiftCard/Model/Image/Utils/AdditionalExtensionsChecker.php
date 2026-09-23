<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\Utils;

/**
 * @api
 */
class AdditionalExtensionsChecker
{
    public const IMAGICK_EXTENSION = 'imagick';
    public const ZIP_EXTENSION = 'zip';

    public function isZipEnabled(): bool
    {
        return extension_loaded(self::ZIP_EXTENSION);
    }

    public function isImagickEnabled(): bool
    {
        return extension_loaded(self::IMAGICK_EXTENSION);
    }
}
