<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Api\Data;

interface HistoryInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const HISTORY_ID = 'history_id';
    public const CUSTOMER_HISTORY_ID = 'customer_history_id';
    public const CUSTOMER_ID = 'customer_id';
    public const IS_DEDUCT = 'is_deduct';
    public const DIFFERENCE = 'difference';
    public const STORE_CREDIT_BALANCE = 'store_credit_balance';
    public const ACTION = 'action';
    public const ACTION_DATA = 'action_data';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const STORE_ID = 'store_id';
    public const IS_VISIBLE_FOR_CUSTOMER = 'is_visible_for_customer';
    public const ADMIN_NAME = 'admin_name';
    public const ORDER_ID = 'order_id';

    /**
     * @return int
     */
    public function getHistoryId();

    /**
     * @param int $historyId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setHistoryId($historyId);

    /**
     * @return int
     */
    public function getCustomerHistoryId();

    /**
     * @param int $customerHistoryId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setCustomerHistoryId($customerHistoryId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return bool
     */
    public function isDeduct();

    /**
     * @param bool $isDeduct
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setIsDeduct($isDeduct);

    /**
     * @return float
     */
    public function getDifference();

    /**
     * @param float $difference
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setDifference($difference);

    /**
     * @return float
     */
    public function getStoreCreditBalance();

    /**
     * @param float $storeCreditBalance
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setStoreCreditBalance($storeCreditBalance);

    /**
     * @return int
     */
    public function getAction();

    /**
     * @param int $action
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getActionData();

    /**
     * @param string $actionData
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setActionData($actionData);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param int $storeId
     *
     * @return \Amasty\StoreCredit\Api\Data\HistoryInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param bool $isVisibleForCustomer
     * @return void
     */
    public function setIsVisibleForCustomer(bool $isVisibleForCustomer): void;

    /**
     * @return bool
     */
    public function isVisibleForCustomer(): bool;

    /**
     * @param string|null $adminName
     * @return void
     */
    public function setAdminName(?string $adminName): void;

    /**
     * @return string|null
     */
    public function getAdminName(): ?string;

    /**
     * @param int|null $orderId
     * @return void
     */
    public function setOrderId(?int $orderId): void;

    /**
     * @return int|null
     */
    public function getOrderId(): ?int;
}
