<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\History;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel implements HistoryInterface
{
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\StoreCredit\Model\History\ResourceModel\History::class);
        $this->setIdFieldName(HistoryInterface::HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function getHistoryId()
    {
        return (int)$this->_getData(HistoryInterface::HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setHistoryId($historyId)
    {
        return $this->setData(HistoryInterface::HISTORY_ID, (int)$historyId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerHistoryId()
    {
        return (int)$this->_getData(HistoryInterface::CUSTOMER_HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerHistoryId($customerHistoryId)
    {
        return $this->setData(HistoryInterface::CUSTOMER_HISTORY_ID, (int)$customerHistoryId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return (int)$this->_getData(HistoryInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(HistoryInterface::CUSTOMER_ID, (int)$customerId);
    }

    /**
     * @inheritdoc
     */
    public function isDeduct()
    {
        return (bool)$this->_getData(HistoryInterface::IS_DEDUCT);
    }

    /**
     * @inheritdoc
     */
    public function setIsDeduct($isDeduct)
    {
        return $this->setData(HistoryInterface::IS_DEDUCT, (bool)$isDeduct);
    }

    /**
     * @inheritdoc
     */
    public function getDifference()
    {
        return (float)$this->_getData(HistoryInterface::DIFFERENCE);
    }

    /**
     * @deprecated since 1.2.1
     * @see \Magento\Framework\Pricing\PriceCurrencyInterface::convertAndFormat
     *
     * @param null|string|bool|int|\Magento\Framework\App\ScopeInterface $scope
     * @param \Magento\Framework\Model\AbstractModel|string|null $currency
     *
     * @return string
     */
    public function getFormatDifference($scope = null, $currency = null)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function setDifference($difference)
    {
        return $this->setData(HistoryInterface::DIFFERENCE, (float)$difference);
    }

    /**
     * @inheritdoc
     */
    public function getStoreCreditBalance()
    {
        return (float)$this->_getData(HistoryInterface::STORE_CREDIT_BALANCE);
    }

    /**
     * @deprecated since 1.2.1
     * @see \Magento\Framework\Pricing\PriceCurrencyInterface::convertAndFormat
     *
     * @param null|string|bool|int|\Magento\Framework\App\ScopeInterface $scope
     * @param \Magento\Framework\Model\AbstractModel|string|null $currency
     *
     * @return string
     */
    public function getFormatStoreCreditBalance($scope = null, $currency = null)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function setStoreCreditBalance($storeCreditBalance)
    {
        return $this->setData(HistoryInterface::STORE_CREDIT_BALANCE, (float)$storeCreditBalance);
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return (int)$this->_getData(HistoryInterface::ACTION);
    }

    /**
     * @inheritdoc
     */
    public function setAction($action)
    {
        return $this->setData(HistoryInterface::ACTION, (int)$action);
    }

    /**
     * @inheritdoc
     */
    public function getActionData()
    {
        return $this->_getData(HistoryInterface::ACTION_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setActionData($actionData)
    {
        return $this->setData(HistoryInterface::ACTION_DATA, $actionData);
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->_getData(HistoryInterface::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage($message)
    {
        return $this->setData(HistoryInterface::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(HistoryInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(HistoryInterface::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(HistoryInterface::STORE_ID);
    }

    /**
     * @param bool $isVisibleForCustomer
     * @return void
     */
    public function setIsVisibleForCustomer(bool $isVisibleForCustomer): void
    {
        $this->setData(HistoryInterface::IS_VISIBLE_FOR_CUSTOMER, $isVisibleForCustomer);
    }

    /**
     * @return bool
     */
    public function isVisibleForCustomer(): bool
    {
        return (bool) $this->_getData(HistoryInterface::IS_VISIBLE_FOR_CUSTOMER);
    }

    public function setAdminName(?string $adminName): void
    {
        $this->setData(self::ADMIN_NAME, $adminName);
    }

    public function getAdminName(): ?string
    {
        return $this->_getData(self::ADMIN_NAME);
    }

    public function setOrderId(?int $orderId): void
    {
        $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOrderId(): ?int
    {
        return $this->getData(self::ORDER_ID) ? (int)$this->getData(self::ORDER_ID) : null;
    }
}
