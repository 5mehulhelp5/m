<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Setup\Operation;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Api\Data\CodePoolInterface;
use Amasty\GiftCard\Model\Code\Repository as CodeRepository;
use Amasty\GiftCard\Model\Code\ResourceModel\Code;
use Amasty\GiftCard\Model\CodePool\ResourceModel\CodePool;
use Amasty\GiftCard\Model\OptionSource\Status;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account;
use Magento\Framework\DB\Select;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @codeCoverageIgnore
 */
class UpdateDuplicateAccounts
{
    private const ACCOUNT_ID_KEY = 'account_id';
    private const NEW_CODE_KEY = 'new_code';

    /**
     * @var CodeRepository
     */
    private $codeRepository;

    /**
     * @var NotifierInterface
     */
    private $notifier;

    /**
     * @var ConsoleOutput
     */
    private $consoleOutput;

    public function __construct(
        CodeRepository $codeRepository,
        NotifierInterface $notifier,
        ConsoleOutput $consoleOutput
    ) {
        $this->codeRepository = $codeRepository;
        $this->notifier = $notifier;
        $this->consoleOutput = $consoleOutput;
    }

    public function execute(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $select = $connection->select()->from(
            ['e' => $setup->getTable(Account::TABLE_NAME)],
            ['e.' . GiftCardAccountInterface::CODE_ID, 'e.' . GiftCardAccountInterface::ACCOUNT_ID]
        )->join(
            ['sub' => $this->getDuplicatesSelect($setup)],
            'sub.' . GiftCardAccountInterface::CODE_ID . ' = e.' . GiftCardAccountInterface::CODE_ID,
            []
        );

        $duplicatesMapping = [];
        foreach ($connection->fetchAll($select) as $match) {
            $duplicatesMapping[$match[GiftCardAccountInterface::CODE_ID]][]
                = $match[GiftCardAccountInterface::ACCOUNT_ID];
        }

        if (!empty($duplicatesMapping)) {
            $updatedAccounts = $this->updateDuplicateAccounts($setup, $duplicatesMapping);
            $this->addNotificationMessages($updatedAccounts);
        }
    }

    private function getDuplicatesSelect(ModuleDataSetupInterface $setup): Select
    {
        $connection = $setup->getConnection();

        return $connection->select()->from(
            $setup->getTable(Account::TABLE_NAME),
            [GiftCardAccountInterface::ACCOUNT_ID, GiftCardAccountInterface::CODE_ID, new \Zend_Db_Expr('COUNT(*)')]
        )->group(
            GiftCardAccountInterface::CODE_ID
        )->having(
            new \Zend_Db_Expr('COUNT(*) > 1')
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $duplicatesMapping [code_id => [account_ids]]
     *
     * @return array [account_id => new_code]
     */
    private function updateDuplicateAccounts(ModuleDataSetupInterface $setup, array $duplicatesMapping): array
    {
        $updatedAccountsMapping = [];
        foreach ($duplicatesMapping as $codeId => $duplicateAccountIds) {
            $duplicateAccountIdsQty = count($duplicateAccountIds);
            $codePoolId = $this->matchCodePoolByCodeId($setup, (int)$codeId);
            $availableCodes = $this->codeRepository->getAvailableCodesByCodePoolId(
                $codePoolId,
                $duplicateAccountIdsQty
            );
            if (count($availableCodes) < $duplicateAccountIdsQty) {
                throw new \RuntimeException(
                    'The Gift Card Account duplicate(s) was found while updating. '
                    . 'But there aren\'t enough gift codes in the Code Pool for new codes automatic generation. '
                    . 'Please generate the codes in the Code Pool and run the command again.'
                );
            }

            $accountInsertData = $codeInsertData = [];
            foreach ($duplicateAccountIds as $duplicateAccountId) {
                /** @var CodeInterface $code */
                $code = array_pop($availableCodes);
                $accountsData = [
                    GiftCardAccountInterface::ACCOUNT_ID => $duplicateAccountId,
                    GiftCardAccountInterface::CODE_ID => $code->getCodeId()
                ];
                $accountInsertData[] = $accountsData;
                $code->setStatus(Status::USED);
                $codeInsertData[] = $code->getData();

                $updatedAccountsMapping[] = [
                    self::ACCOUNT_ID_KEY => $duplicateAccountId,
                    self::NEW_CODE_KEY => $code->getCode()
                ];
            }
            $connection = $setup->getConnection();
            $connection->insertOnDuplicate(
                $setup->getTable(Account::TABLE_NAME),
                $accountInsertData
            );
            $connection->insertOnDuplicate(
                $setup->getTable(Code::TABLE_NAME),
                $codeInsertData
            );
        }

        return $updatedAccountsMapping;
    }

    private function matchCodePoolByCodeId(ModuleDataSetupInterface $setup, int $codeId): int
    {
        $connection = $setup->getConnection();
        $select = $connection->select()->from(
            ['e' => $setup->getTable(CodePool::TABLE_NAME)],
            CodePoolInterface::CODE_POOL_ID
        )->joinLeft(
            ['c' => $setup->getTable(Code::TABLE_NAME)],
            'e.' . CodePoolInterface::CODE_POOL_ID . ' = c.' . CodeInterface::CODE_POOL_ID,
            []
        )->where(
            'c.' . CodeInterface::CODE_ID . ' = ?',
            $codeId
        );

        $codePoolId = $connection->fetchOne($select);
        if (!$codePoolId) {
            //will use first code pool in the list if no matched found
            $codePoolId = $connection->fetchOne($connection->select()->from(
                ['e' => $setup->getTable(CodePool::TABLE_NAME)],
                CodePoolInterface::CODE_POOL_ID
            ));
        }

        return (int)$codePoolId;
    }

    private function addNotificationMessages(array $updatedAccounts): void
    {
        $msg = 'The Gift Card Account duplicate(s) was found while updating.'
            . ' Please be aware that the new codes were created for it: ';
        foreach ($updatedAccounts as $account) {
            $msg .= 'account ID ' . $account[self::ACCOUNT_ID_KEY] . ' -> code ' . $account[self::NEW_CODE_KEY] . ', ';
        }
        $msg = rtrim($msg, ', ');
        $msg .= '. We recommend you to notify the clients about the gift card codes changing.';

        $this->consoleOutput->writeln('<comment>' . $msg . '</comment>');
        $this->notifier->addMajor('Gift Card Account duplicate(s).', $msg);
    }
}
