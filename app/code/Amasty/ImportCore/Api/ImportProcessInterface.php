<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

interface ImportProcessInterface extends ExtensibleDataInterface
{
    /**
     * Get process identity
     *
     * @return string|null
     */
    public function getIdentity(): ?string;

    /**
     * @return EntityConfigInterface
     */
    public function getEntityConfig(): EntityConfigInterface;

    /**
     * @return ProfileConfigInterface
     */
    public function getProfileConfig(): ProfileConfigInterface;

    /**
     * @return ImportResultInterface
     */
    public function getImportResult(): ImportResultInterface;

    /**
     * Add behavior result of specified entity code to import process
     *
     * @param string $entityCode
     * @param BehaviorResultInterface $result
     * @return ImportProcessInterface
     */
    public function addProcessedEntityResult(
        string $entityCode,
        BehaviorResultInterface $result
    ): ImportProcessInterface;

    /**
     * Get behavior result of specified entity.
     * Returns behavior results of all entities if $entityCode == null
     *
     * @param string|null $entityCode
     * @return BehaviorResultInterface|BehaviorResultInterface[]|null
     */
    public function getProcessedEntityResult(?string $entityCode = null);

    /**
     * @return ImportProcessInterface
     */
    public function resetProcessedEntitiesResult(): ImportProcessInterface;

    /**
     * @param string $message
     * @return ImportProcessInterface
     */
    public function addCriticalMessage(string $message): ImportProcessInterface;

    /**
     * @param string $message
     * @return ImportProcessInterface
     */
    public function addErrorMessage(string $message): ImportProcessInterface;

    /**
     * @param string $message
     * @return ImportProcessInterface
     */
    public function addWarningMessage(string $message): ImportProcessInterface;

    /**
     * @param string $message
     * @return ImportProcessInterface
     */
    public function addInfoMessage(string $message): ImportProcessInterface;

    /**
     * @param string $message
     * @return ImportProcessInterface
     */
    public function addDebugMessage(string $message): ImportProcessInterface;

    /**
     * @param int $type
     * @param string $message
     * @return ImportProcessInterface
     */
    public function addMessage(int $type, string $message): ImportProcessInterface;

    /**
     * Add validation error to import process
     *
     * @param string $message
     * @param int $rowNumber
     * @param string|null $entityName
     * @return ImportProcessInterface
     */
    public function addValidationError(
        string $message,
        int $rowNumber,
        ?string $entityName = null
    ): ImportProcessInterface;

    /**
     * @param array $rowNumbers
     * @return ImportProcessInterface
     */
    public function addSkippedRowNumbers(array $rowNumbers): ImportProcessInterface;
    public function setHasNonEmptyBatch(bool $hasNonEmptyBatch): ImportProcessInterface;

    /**
     * Get import data
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Set import data to import process
     *
     * @param array $data
     * @return ImportProcessInterface
     */
    public function setData(array $data): ImportProcessInterface;

    /**
     * Checks if the process can be forked
     *
     * @return bool
     */
    public function canFork(): bool;

    /**
     * Forks the process
     *
     * @return int PID of child process
     */
    public function fork(): int;

    /**
     * Checks if the current process is child
     *
     * @return bool
     */
    public function isChildProcess(): bool;

    /**
     * Returns number of validation errors
     *
     * @return int
     */
    public function getErrorQuantity(): int;

    /**
     * Increments validation errors counter
     *
     * @return void
     */
    public function increaseErrorQuantity(): void;

    /**
     * @return int
     */
    public function getBatchNumber(): int;

    /**
     * @param int $batchNumber
     * @return void
     */
    public function setBatchNumber(int $batchNumber): void;

    /**
     * @param int $batchQty
     * @return void
     */
    public function setBatchQty(int $batchQty): void;

    /**
     * @return int
     */
    public function getBatchQty(): int;

    /**
     * @param bool $isReached
     * @return void
     */
    public function setLastBatchReached(bool $isReached): void;

    /**
     * @return bool
     */
    public function isLastBatchReached(): bool;

    /**
     * Extension point for customizations to set extension attributes of ImportProcess class
     */
    public function initialize(): ImportProcessInterface;

    /**
     * @return \Amasty\ImportCore\Api\ImportProcessExtensionInterface
     */
    public function getExtensionAttributes(): ImportProcessExtensionInterface;

    /**
     * @param \Amasty\ImportCore\Api\ImportProcessExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        ImportProcessExtensionInterface $extensionAttributes
    ): void;
}
