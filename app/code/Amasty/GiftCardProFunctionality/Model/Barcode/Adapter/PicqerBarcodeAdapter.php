<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model\Barcode\Adapter;

use Magento\Framework\ObjectManagerInterface;
use Picqer\Barcode\BarcodeGenerator;

class PicqerBarcodeAdapter implements BarcodeGeneratorAdapterInterface
{
    public const DEFAULT_WIDTH_FACTOR = 1; // @deprecated
    public const DEFAULT_HEIGHT = 30; // @deprecated
    public const DEFAULT_FOREGROUND_COLOR = 'black';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function getBarcode(
        string $giftcardCode,
        int $width = BarcodeGeneratorAdapterInterface::DEFAULT_WIDTH,
        int $height = BarcodeGeneratorAdapterInterface::DEFAULT_HEIGHT
    ): string {
        if (!class_exists(BarcodeGenerator::class)) {
            throw new \RuntimeException(
                'PHP library \'picqer/php-barcode-generator\' not found.'
                . 'Please run \'composer require picqer/php-barcode-generator\' command to install it.'
            );
        }
        $barcodeRenderer = $this->objectManager->get(BarcodeRenderer::class);

        return $barcodeRenderer->render($giftcardCode, $width, $height);
    }
}
