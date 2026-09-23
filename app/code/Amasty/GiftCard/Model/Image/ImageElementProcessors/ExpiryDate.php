<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\ImageElementProcessors;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Model\Image\Utils\ImageElementCssMerger;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\App\Emulation;

class ExpiryDate implements ImageElementProcessorInterface
{
    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        DateTime $dateTime,
        ?Emulation $emulation = null,
        ?TimezoneInterface $timezone = null
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->dateTime = $dateTime;
        $this->emulation = $emulation ?? ObjectManager::getInstance()->get(Emulation::class);
        $this->timezone = $timezone ?? ObjectManager::getInstance()->get(TimezoneInterface::class);
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $valueSource */
        $valueSource = $imageElement->getValueDataSource();
        if (!$valueSource->getExpiredDate()) {
            return '';
        }

        $value = $this->getValue($valueSource);
        return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
            . $value
            . '</span>';
    }

    public function getDefaultValue(): string
    {
        return __('Expiry Date: 01 January 2021')->render();
    }

    private function getValue(GiftCardAccountInterface $valueSource): string
    {
        $orderItem = $valueSource->getOrderItem();
        if (null !== $orderItem) {
            $order = $orderItem->getOrder();
            $storeId = $order->getStore()->getStoreId();
        } else {
            $storeId = $valueSource->getStoreId();
        }
        $this->emulation->startEnvironmentEmulation($storeId);
        $formattedDate = $this->timezone->formatDateTime(
            $valueSource->getExpiredDate(),
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            null
        );
        $value = __('Expiry Date: %1', $formattedDate)->render();
        $this->emulation->stopEnvironmentEmulation();

        return $value;
    }
}
