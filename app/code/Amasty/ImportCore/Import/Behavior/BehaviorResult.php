<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Behavior;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;

class BehaviorResult implements BehaviorResultInterface
{
    /**
     * @var array
     */
    private $newIds = [];

    /**
     * @var array
     */
    private $updatedIds = [];

    /**
     * @var array
     */
    private $deletedIds = [];

    /**
     * @var array
     */
    private $affectedIds = [];

    /**
     * @return array
     */
    public function getNewIds(): array
    {
        return $this->newIds;
    }

    /**
     * @param array $newIds
     */
    public function setNewIds(array $newIds): void
    {
        $this->newIds = $newIds;
    }

    /**
     * @return array
     */
    public function getUpdatedIds(): array
    {
        return $this->updatedIds;
    }

    /**
     * @param array $updatedIds
     */
    public function setUpdatedIds(array $updatedIds): void
    {
        $this->updatedIds = $updatedIds;
    }

    /**
     * @return array
     */
    public function getDeletedIds(): array
    {
        return $this->deletedIds;
    }

    /**
     * @param array $deletedIds
     */
    public function setDeletedIds(array $deletedIds): void
    {
        $this->deletedIds = $deletedIds;
    }

    /**
     * @param array $affectedIds
     */
    public function setAffectedIds(array $affectedIds): void
    {
        $this->affectedIds = $affectedIds;
    }

    public function getAffectedIds(): array
    {
        return empty($this->affectedIds)
            ? array_merge($this->getDeletedIds(), $this->getNewIds(), $this->getUpdatedIds())
            : $this->affectedIds;
    }

    public function merge(BehaviorResultInterface $anotherResult): void
    {
        $this->newIds = array_merge($this->newIds, $anotherResult->getNewIds());
        $this->updatedIds = array_merge($this->updatedIds, $anotherResult->getUpdatedIds());
        $this->deletedIds = array_merge($this->deletedIds, $anotherResult->getDeletedIds());

        sort($this->newIds);
        sort($this->updatedIds);
        sort($this->deletedIds);

        if (!empty($this->affectedIds)) {
            $this->affectedIds = array_merge($this->affectedIds, $anotherResult->getAffectedIds());
        }
    }
}
