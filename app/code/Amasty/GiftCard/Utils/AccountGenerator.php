<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Utils;

use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Model\Config\Source\GiftCardType;
use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\GiftCard\Attributes;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\ItemRepository;

class AccountGenerator
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var ItemRepository
     */
    private $orderItemRepository;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ManagerInterface $eventManager,
        ItemRepository $orderItemRepository,
        DateTime $date,
        ConfigProvider $configProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ?TimezoneInterface $timezone = null // TODO move to not optional
    ) {
        $this->eventManager = $eventManager;
        $this->orderItemRepository = $orderItemRepository;
        $this->date = $date;
        $this->configProvider = $configProvider;
        $this->messageManager = $messageManager;
        $this->timezone = $timezone ?? ObjectManager::getInstance()->get(TimezoneInterface::class);
    }

    /**
     * @param OrderItem $orderItem
     * @param int $qty
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateFromOrderItem(OrderItem $orderItem, int $qty)
    {
        if ($qty <= 0) {
            return;
        }
        $hasFailedCodes = false;
        $options = $orderItem->getProductOptions();
        $amount = (float)($options[GiftCardOptionInterface::GIFTCARD_AMOUNT] ?? 0.);
        $websiteId = (int)$orderItem->getStore()->getWebsiteId();
        $storeId = (int)$orderItem->getStoreId();

        $dateDelivery = $this->getDeliveryDate($options);
        $lifetime = $options[Attributes::GIFTCARD_LIFETIME] ?? $this->configProvider->getLifetime();
        $gCardType = $options[Attributes::GIFTCARD_TYPE] ?? null;
        $codePool = $options[Attributes::CODE_SET] ?? null;

        $accountData = new \Magento\Framework\DataObject(); //used in GiftCardAccount to create account from DataObject
        $accountData->setOrderItemId((int)$orderItem->getItemId())
            ->setInitialValue($amount)
            ->setCurrentValue($amount)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setImageId($options[GiftCardOptionInterface::IMAGE] ?? '')
            ->setDateDelivery($dateDelivery)
            ->setCodePool($codePool)
            ->setRecipientEmail($options[GiftCardOptionInterface::RECIPIENT_EMAIL] ?? '')
            ->setMobilenumber($options[GiftCardOptionInterface::RECIPIENT_PHONE] ?? '')
            ->setUsage($options[GiftCardOptionInterface::USAGE] ?? null);

        if ($lifetime) {
            $expiredDate = $this->date->gmtDate('Y-m-d H:i:s', $dateDelivery . "+{$lifetime} days");
            $accountData->setExpiredDate($expiredDate);
        }

        if (!$this->configProvider->isAllowUseThemselves()) {
            $accountData->setCustomerCreatedId($orderItem->getOrder()->getCustomerId());
        }
        $codes = $options[GiftCardOptionInterface::GIFTCARD_CREATED_CODES] ?? [];
        $generatedCodes = [];

        for ($i = 0; $i < $qty; $i++) {
            try {
                $this->eventManager->dispatch(
                    'amasty_giftcard_account_create',
                    ['account_data' => $accountData]
                );
                $generatedCodes[] = $accountData->getCode();
            } catch (LocalizedException $e) {
                $hasFailedCodes = true;
            }
        }
        $options[GiftCardOptionInterface::GIFTCARD_CREATED_CODES] = array_merge($codes, $generatedCodes);
        $orderItem->setProductOptions($options); //need for displaying generated account codes on product page

        if ($orderItem->getId()) {
            $this->orderItemRepository->save($orderItem);
        }

        if ($generatedCodes
            && $gCardType != GiftCardType::TYPE_PRINTED
            && strtotime($dateDelivery) <= strtotime($this->date->gmtDate('Y-m-d'))
        ) {
            $this->eventManager->dispatch(
                'amasty_giftcard_send_order_cards',
                ['codes' => $generatedCodes]
            );
        }

        if ($hasFailedCodes) {
            $this->messageManager->addWarningMessage(
                __(
                    'Some gift card accounts were not created properly.
                    Please create them manually.'
                )
            );
        }
    }

    private function getDeliveryDate(array $options): string
    {
        return $options[GiftCardOptionInterface::DELIVERY_DATE] ?? $this->date->gmtDate('Y-m-d');
    }
}
