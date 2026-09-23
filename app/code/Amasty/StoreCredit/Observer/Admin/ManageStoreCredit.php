<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Observer\Admin;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface;
use Amasty\StoreCredit\Model\History\MessageProcessor;
use Amasty\StoreCredit\Model\StoreCredit\StoreCreditRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class ManageStoreCredit implements ObserverInterface
{
    public const ACL_RESOURCE = 'Amasty_StoreCredit::customer';

    /**
     * @var ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var StoreCreditRepository
     */
    private $storeCreditRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ManageCustomerStoreCreditInterface $manageCustomerStoreCredit,
        AuthorizationInterface $authorization,
        StoreCreditRepository $storeCreditRepository,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        RequestInterface $request
    ) {
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
        $this->authorization = $authorization;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        if ($this->authorization->isAllowed(self::ACL_RESOURCE)) {
            /** @var CustomerInterface $customer */
            $customer = $observer->getData('customer');

            if ($amount = $this->retrieveAmount((int) $customer->getId())) {
                $action = $amount < 0
                    ? MessageProcessor::ADMIN_BALANCE_CHANGE_MINUS
                    : MessageProcessor::ADMIN_BALANCE_CHANGE_PLUS;

                $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                    $customer->getId(),
                    $amount,
                    $action,
                    [],
                    null,
                    $this->request->getParam(StoreCreditInterface::ADMIN_COMMENT, ''),
                    (bool) $this->request->getParam(HistoryInterface::IS_VISIBLE_FOR_CUSTOMER)
                );
            }
        }
    }

    private function retrieveAmount(int $customerId): ?float
    {
        if ($amount = $this->request->getParam(StoreCreditInterface::ADD_OR_SUBTRACT)) {
            $amount = (float)$amount;
            $currentBalance = $this->storeCreditRepository->getByCustomerId($customerId)->getStoreCredit();
            if ($amount + $currentBalance < 0) {
                $amount = -$currentBalance;
            }
        } else {
            $amount = null;
        }

        return $amount;
    }
}
