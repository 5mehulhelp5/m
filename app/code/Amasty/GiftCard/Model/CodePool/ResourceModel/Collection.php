<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\CodePool\ResourceModel;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Api\Data\CodePoolInterface;
use Amasty\GiftCard\Model\Code\ResourceModel\Code;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\GiftCard\Model\CodePool\CodePool::class,
            \Amasty\GiftCard\Model\CodePool\ResourceModel\CodePool::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Format columns for display in listing
     *
     * @return Collection
     */
    public function addGiftCodeCountColumns(): Collection
    {
        $codeTable = $this->getTable(Code::TABLE_NAME);
        $subSelect = $this->getConnection()->select()
            ->from(['c' => $codeTable], [CodeInterface::CODE_POOL_ID])
            ->columns([
                'qty' => new \Zend_Db_Expr('COUNT(c.' . CodeInterface::CODE_ID . ')'),
                'qty_unused' => new \Zend_Db_Expr(
                    'IFNULL(SUM(CASE WHEN c.' . CodeInterface::STATUS . ' = 0 THEN 1 END), 0)'
                )
            ])->group('c.' . CodeInterface::CODE_POOL_ID);
        $this->getSelect()->joinLeft(
            ['code' => $subSelect],
            'main_table.' . CodePoolInterface::CODE_POOL_ID . ' = code.' . CodeInterface::CODE_POOL_ID,
            ['code.qty', 'code.qty_unused']
        );

        return $this;
    }

    public function toOptionArray(): array
    {
        return $this->_toOptionArray(CodePoolInterface::CODE_POOL_ID, CodePoolInterface::TITLE);
    }
}
