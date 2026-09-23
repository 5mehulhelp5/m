<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Pdf;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\Repository as ImageRepository;
use Amasty\GiftCard\Model\Pdf\ImageToPdf\ImageToPdfAdapterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * @api
 */
class PdfImageConverter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImageToPdf\ImageToPdfAdapterInterface
     */
    private $imageToPdfAdapter;

    /**
     * @var array
     */
    private $generationCache = [];

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    public function __construct(
        LoggerInterface $logger,
        ImageToPdfAdapterInterface $imageToPdfAdapter,
        ImageRepository $imageRepository
    ) {
        $this->logger = $logger;
        $this->imageToPdfAdapter = $imageToPdfAdapter;
        $this->imageRepository = $imageRepository;
    }

    public function convert(string $imageHtml, string $code = '', ?int $imageId = null): string
    {
        if (!empty($code) && isset($this->generationCache[$code])) {
            return $this->generationCache[$code];
        }

        try {
            $imageSize = $this->prepareImageSize((int)$imageId);
            $pdfString = $this->createPdfPageFromImageParams($imageHtml, $imageSize);
        } catch (\Exception $e) {
            $pdfString = '';
            $this->logger->critical($e->getMessage());
        }

        if (!empty($code)) {
            $this->generationCache[$code] = $pdfString;
        }

        return $pdfString;
    }

    private function createPdfPageFromImageParams(string $imageHtml, ?array $imageSize = null): string
    {
        return $this->imageToPdfAdapter->render($imageHtml, $imageSize);
    }

    private function prepareImageSize(int $imageId): array
    {
        $imageSize = [0.0, 0.0, ImageInterface::DEFAULT_HEIGHT, ImageInterface::DEFAULT_WIDTH];
        if ($imageId !== 0) {
            try {
                $image = $this->imageRepository->getById($imageId);
                $imageSize[2] = $image->getHeight();
                $imageSize[3] = $image->getWidth();
            } catch (NoSuchEntityException $e) {
                $this->logger->critical($e);
            }
        }

        return $imageSize;
    }
}
