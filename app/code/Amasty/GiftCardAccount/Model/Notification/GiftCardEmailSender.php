<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Notification;

use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Amasty\GiftCardAccount\Api\GiftCardEmailSenderInterface;
use Amasty\GiftCardAccount\Model\OptionSource\AccountStatus;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class GiftCardEmailSender implements GiftCardEmailSenderInterface
{
    /**
     * @var GiftCardAccountRepositoryInterface
     */
    private $giftCardAccountRepository;

    /**
     * @var NotificationsApplier
     */
    private $notificationsApplier;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        GiftCardAccountRepositoryInterface $giftCardAccountRepository,
        NotificationsApplier $notificationsApplier,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->giftCardAccountRepository = $giftCardAccountRepository;
        $this->notificationsApplier = $notificationsApplier;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @throws LocalizedException
     */
    public function send(
        int $accountId,
        int $storeId,
        ?string $recipientEmail = null,
        ?string $recipientName = null
    ): bool {
        try {
            $account = $this->giftCardAccountRepository->getById($accountId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Account with the specified ID "%1" does not exist', $accountId));
        }

        if ($account->getStatus() !== AccountStatus::STATUS_ACTIVE) {
            throw new LocalizedException(__('You can\'t send email for inactive account.'));
        }

        $recipientEmail = $recipientEmail ?: $account->getRecipientEmail();
        if (!$recipientEmail) {
            throw new LocalizedException(
                __('Can\'t send email. Please make sure that field "Recipient Email" is filled.')
            );
        }

        try {
            $this->storeManager->getStore($storeId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(
                __('Store with the specified ID "%1" does not exist.', $storeId)
            );
        }

        try {
            $this->notificationsApplier->apply(
                NotifiersProvider::EVENT_WEBAPI_ACCOUNT_SEND,
                $account,
                $recipientName,
                $recipientEmail,
                $storeId ?? Store::DEFAULT_STORE_ID
            );

            return true;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            throw new LocalizedException(__('Something went wrong while sending email.'));
        }
    }
}
