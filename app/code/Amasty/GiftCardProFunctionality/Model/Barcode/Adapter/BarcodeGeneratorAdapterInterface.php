<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model\Barcode\Adapter;

interface BarcodeGeneratorAdapterInterface
{
    public const DEFAULT_WIDTH = 200;
    public const DEFAULT_HEIGHT = 30;

    /**
     * Return an HTML representation of the barcode containing the gift card code.
     *
     * @param string $giftcardCode
     * @return string
     * @throws \RuntimeException
     */
    public function getBarcode(
        string $giftcardCode,
        int $width = self::DEFAULT_WIDTH,
        int $height = self::DEFAULT_HEIGHT
    ): string;
}
