<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\GiftCardAccount\Model\CustomerCard\ResourceModel\CustomerCard;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class DeleteDuplicateCustomerCardRecords implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ModuleDataSetupInterface $setup,
        LoggerInterface $logger
    ) {
        $this->setup = $setup;
        $this->logger = $logger;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): DeleteDuplicateCustomerCardRecords
    {
        $customerCardTable = $this->setup->getTable(CustomerCard::TABLE_NAME);
        if ($this->setup->tableExists($customerCardTable)) {
            try {
                $this->deleteDuplicate();
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $this;
    }

    private function deleteDuplicate(): void
    {
        $customerCardTable = $this->setup->getTable(CustomerCard::TABLE_NAME);
        $connection = $this->setup->getConnection();
        $duplicateRows = $connection->select()
            ->from($customerCardTable, ['account_id', 'customer_id'])
            ->group(['account_id', 'customer_id'])
            ->having('COUNT(*) > 1');
        $uniqueCustomerCard = $connection->select()
            ->from($customerCardTable, ['customer_card_id'])
            ->group(['account_id', 'customer_id'])
            ->having('COUNT(*) > 1');
        $duplicateCustomerCard = $connection->select()
            ->from($customerCardTable, ['customer_card_id'])
            ->where('(account_id, customer_id) IN ?', $duplicateRows)
            ->where('customer_card_id NOT IN ?', $uniqueCustomerCard);
        $connection->delete(
            $customerCardTable,
            ['customer_card_id IN (?)' => $connection->fetchCol($duplicateCustomerCard)]
        );
    }
}
