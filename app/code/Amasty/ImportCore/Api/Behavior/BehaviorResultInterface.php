<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Behavior;

interface BehaviorResultInterface
{
    /**
     * @return array
     */
    public function getNewIds(): array;

    /**
     * @param array $ids
     */
    public function setNewIds(array $ids): void;

    /**
     * @return array
     */
    public function getUpdatedIds(): array;

    /**
     * @param array $ids
     */
    public function setUpdatedIds(array $ids): void;

    /**
     * @return array
     */
    public function getDeletedIds(): array;

    /**
     * @param array $ids
     */
    public function setDeletedIds(array $ids): void;

    /**
     * @param array $ids
     */
    public function setAffectedIds(array $ids): void;

    /**
     * Return ids of all affected entities during behavior execution
     *
     * @return array
     */
    public function getAffectedIds(): array;

    /**
     * Merges specified behavior result
     *
     * @param BehaviorResultInterface $anotherResult
     * @return void
     */
    public function merge(BehaviorResultInterface $anotherResult): void;
}
