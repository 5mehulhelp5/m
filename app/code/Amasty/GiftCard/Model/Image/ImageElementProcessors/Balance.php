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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Currency\Data\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Balance implements ImageElementProcessorInterface
{
    public const LOCAL_CODE_CONFIG_PATH = 'general/locale/code';

    /**
     * @var ImageElementCssMerger
     */
    private $imageElementCssMerger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ImageElementCssMerger $imageElementCssMerger,
        StoreManagerInterface $storeManager,
        CurrencyInterface $localeCurrency,
        ?ScopeConfigInterface $scopeConfig = null
    ) {
        $this->imageElementCssMerger = $imageElementCssMerger;
        $this->storeManager = $storeManager;
        $this->localeCurrency = $localeCurrency;
        $this->scopeConfig = $scopeConfig ?? ObjectManager::getInstance()->get(ScopeConfigInterface::class);
    }

    public function generateHtml(ImageElementsInterface $imageElement): string
    {
        return '<span style="' . $this->imageElementCssMerger->merge($imageElement) . '">'
            . $this->getValue($imageElement)
            . '</span>';
    }

    public function getDefaultValue(): string
    {
        return __('$100.00')->render();
    }

    private function getValue(ImageElementsInterface $imageElement): string
    {
        /** @var GiftCardAccountInterface $valueSource */
        $valueSource = $imageElement->getValueDataSource();
        $orderItem = $valueSource->getOrderItem();
        if (null !== $orderItem) {
            $order = $orderItem->getOrder();
            $storeId = $order->getStore()->getStoreId();
            $websiteId = $order->getStore()->getWebsiteId();
        } else {
            $storeId = $valueSource->getStoreId();
            $websiteId = $valueSource->getWebsiteId();
        }
        $storeLocal = $this->scopeConfig->getValue(self::LOCAL_CODE_CONFIG_PATH, ScopeInterface::SCOPE_STORE, $storeId);
        $websiteLocal = $this->scopeConfig->getValue(
            self::LOCAL_CODE_CONFIG_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        $currency = $this->localeCurrency->getCurrency(
            $this->storeManager->getWebsite($valueSource->getWebsiteId())->getBaseCurrencyCode()
        );
        if ($storeLocal === $websiteLocal) {
            $value = $currency->toCurrency(sprintf("%f", $valueSource->getInitialValue()));
        } else {
            $value = $currency->toCurrency(
                sprintf("%f", $valueSource->getInitialValue()),
                ['display' => Currency::USE_SHORTNAME]
            );
        }

        return $value;
    }
}
