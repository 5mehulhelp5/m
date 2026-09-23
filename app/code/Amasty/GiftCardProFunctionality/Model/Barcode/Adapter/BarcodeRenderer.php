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
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\Helpers\ColorHelper;
use Picqer\Barcode\Renderers\HtmlRenderer;

class BarcodeRenderer extends BarcodeGenerator
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function render(
        string $giftcardCode,
        int $width = BarcodeGeneratorAdapterInterface::DEFAULT_WIDTH,
        int $height = BarcodeGeneratorAdapterInterface::DEFAULT_HEIGHT
    ) {
        $colorHelper = $this->objectManager->create(ColorHelper::class);
        $colorArr = $colorHelper->getArrayFromColorString(PicqerBarcodeAdapter::DEFAULT_FOREGROUND_COLOR);

        $renderer = $this->objectManager->create(HtmlRenderer::class);
        $renderer->setForegroundColor($colorArr);

        $barcodeData = $this->getBarcodeData($giftcardCode, BarcodeGeneratorHTML::TYPE_CODE_128);

        return $renderer->render($barcodeData, $width, $height);
    }
}
