<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Plugin\GiftCard\Image\EmailImageProvider;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\EmailImageProvider;
use Amasty\GiftCardAccount\Model\Image\Utils\ImageHtmlToFileConverter;

class GenerateImage
{
    /**
     * @var ImageHtmlToFileConverter
     */
    private $imageHtmlToFileConverter;

    public function __construct(
        ImageHtmlToFileConverter $imageHtmlToFileConverter
    ) {
        $this->imageHtmlToFileConverter = $imageHtmlToFileConverter;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        EmailImageProvider $subject,
        string $result,
        ImageInterface $image,
        string $code
    ): string {
        return $this->imageHtmlToFileConverter->convert($image, $code, $result);
    }
}
