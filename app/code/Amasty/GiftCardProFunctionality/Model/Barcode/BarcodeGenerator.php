<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model\Barcode;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCardProFunctionality\Model\Barcode\Adapter\BarcodeGeneratorAdapterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class BarcodeGenerator
{
    /**
     * @var BarcodeGeneratorAdapterInterface
     */
    private $barcodeGeneratorAdapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        BarcodeGeneratorAdapterInterface $barcodeGeneratorAdapter,
        LoggerInterface $logger,
        ?ManagerInterface $messageManager = null
    ) {
        $this->barcodeGeneratorAdapter = $barcodeGeneratorAdapter;
        $this->logger = $logger;
        $this->messageManager = $messageManager ?? ObjectManager::getInstance()->get(ManagerInterface::class);
    }

    public function generate(string $giftcardCode, ?ImageElementsInterface $imageElement = null): ?string
    {
        try {
            return $this->barcodeGeneratorAdapter->getBarcode(
                $giftcardCode,
                $imageElement ? $imageElement->getWidth() : BarcodeGeneratorAdapterInterface::DEFAULT_WIDTH,
                $imageElement ? $imageElement->getHeight() : BarcodeGeneratorAdapterInterface::DEFAULT_HEIGHT
            );
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addWarningMessage($e->getMessage());

            return null;
        }
    }
}
