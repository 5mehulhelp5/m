<?php

namespace Crimson\ProductBackorderNotification\Plugin\Checkout\Model;

use Crimson\ProductBackorderNotification\Helper\BackorderNotification as BackorderNotificationHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;

class DefaultConfigProviderPlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var QuoteItemRepository
     */
    protected $quoteItemRepository;

    /**
     * @var ItemResolverInterface
     */
    protected $itemResolver;

    /**
     * @var BackorderNotificationHelper
     */
    protected $backorderNotificationHelper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param CheckoutSession $checkoutSession
     * @param QuoteItemRepository $quoteItemRepository
     * @param ItemResolverInterface $itemResolver
     * @param BackorderNotificationHelper $backorderNotificationHelper
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        QuoteItemRepository $quoteItemRepository,
        ItemResolverInterface $itemResolver,
        BackorderNotificationHelper $backorderNotificationHelper,
        ProductRepositoryInterface $productRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->itemResolver = $itemResolver;
        $this->backorderNotificationHelper = $backorderNotificationHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(DefaultConfigProvider $subject, array $result): array
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();
        $result['backorderNotificationData'] = $this->getBackorderNotifications($quoteId);

        return $result;
    }

    /**
     * @param $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getBackorderNotifications($cartId)
    {
        $itemData = [];
        $items = $this->quoteItemRepository->getList($cartId);
        foreach ($items as $cartItem) {
            /** @var \Magento\Quote\Model\Quote\Item $cartItem */
            $itemData[$cartItem->getItemId()] = $this->getProductBackorderNotificationData($cartItem);
        }
        return $itemData;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return bool|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductBackorderNotificationData($cartItem)
    {
        if ($cartItem->getProduct()->getTypeId() === Configurable::TYPE_CODE && $cartItem->getHasChildren()) {
            $childItem = $cartItem->getChildren()[0];
            $finalProduct = $childItem->getProduct();
        } else {
            $finalProduct = $this->itemResolver->getFinalProduct($cartItem);
        }
        $product = $this->productRepository->getById($finalProduct->getId());
        $isDisplay = $this->backorderNotificationHelper->getIsDisplayBackorderInformation($product);
        if ($isDisplay) {
            return $this->backorderNotificationHelper->getBackorderMessage($product);
        }
    }
}
