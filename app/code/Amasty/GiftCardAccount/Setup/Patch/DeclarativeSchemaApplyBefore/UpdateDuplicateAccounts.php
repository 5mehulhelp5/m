<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account;
use Amasty\GiftCardAccount\Setup\Operation\UpdateDuplicateAccounts as UpdateDuplicateAccountsOperation;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateDuplicateAccounts implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var UpdateDuplicateAccountsOperation
     */
    private $updateDuplicateAccounts;

    public function __construct(
        ModuleDataSetupInterface $setup,
        State $appState,
        UpdateDuplicateAccountsOperation $updateDuplicateAccounts
    ) {
        $this->setup = $setup;
        $this->appState = $appState;
        $this->updateDuplicateAccounts = $updateDuplicateAccounts;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $accountTable = $this->setup->getTable(Account::TABLE_NAME);
        if ($this->setup->tableExists($accountTable)) {
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$this->updateDuplicateAccounts, 'execute'],
                [$this->setup]
            );
        }
    }
}
