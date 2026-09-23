<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Model\Image\ImageElementProcessors\ImageElementProcessorInterface;
use Amasty\GiftCard\Model\Image\Utils\ImageElementCssMerger;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardProFunctionality\Model\Barcode\BarcodeGenerator;
use Magento\Framework\View\Asset\Repository;

class Barcode implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var BarcodeGenerator
     */
    private $barcodeGenerator;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        Repository $assetRepo,
        BarcodeGenerator $barcodeGenerator
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->assetRepo = $assetRepo;
        $this->barcodeGenerator = $barcodeGenerator;
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $dataSource */
        $dataSource = $imageElement->getValueDataSource();

        if ($barcodeHtml = $this->barcodeGenerator->generate($dataSource->getCodeModel()->getCode(), $imageElement)) {
            $firstStyleMatched = [];
            preg_match('/style=\"[^"]+"/m', $barcodeHtml, $firstStyleMatched);
            $replacement = str_replace(';"', ';background-color: white;"', $firstStyleMatched[0]);

            return '<div style="' . $this->imageElementCssMerger->merge($imageElement) . '">' . preg_replace(
                '/style=\"[^"]+"/m',
                $replacement,
                $barcodeHtml,
                1
            ) . '</div>';
        }

        return '';
    }

    public function getDefaultValue(): string
    {
        $barcodePlaceholderUrl = $this->assetRepo->getUrl('Amasty_GiftCardProFunctionality::images/barcode.png');

        if ($barcodePlaceholderUrl) {
            return $barcodePlaceholderUrl;
        }

        return '';
    }
}
