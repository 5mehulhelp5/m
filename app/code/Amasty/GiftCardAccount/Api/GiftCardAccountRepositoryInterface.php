<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Api;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

/**
 * @api
 */
interface GiftCardAccountRepositoryInterface
{
    /**
     * Get account by id
     *
     * @param int $id
     * @param bool $reset
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id, bool $reset = false): GiftCardAccountInterface;

    /**
     * Get account by code
     *
     * @param string $code
     * @param bool $reset
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode(string $code, bool $reset = false): GiftCardAccountInterface;

    /**
     * Get accounts by order id
     *
     * @param int $orderId
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface[]
     */
    public function getByOrderId(int $orderId): array;

    /**
     * Save account
     *
     * @param \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface $account
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(GiftCardAccountInterface $account): GiftCardAccountInterface;

    /**
     * Delete account
     *
     * @param \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface $account
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(GiftCardAccountInterface $account): bool;

    /**
     * Delete account by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;

    /**
     * @param int $customerId
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAccountsByCustomerId(int $customerId): array;

    /**
     * Get all accounts
     *
     * @return \Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(): array;
}
