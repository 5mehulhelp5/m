<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;

interface ImportResultInterface
{
    public const MESSAGE_CRITICAL = 50;
    public const MESSAGE_ERROR = 40;
    public const MESSAGE_WARNING = 30;
    public const MESSAGE_INFO = 20;
    public const MESSAGE_DEBUG = 10;

    public const STAGE_INITIAL = 'initial';

    /**
     * Marks the import as terminated
     *
     * @param bool $failed
     * @return void
     */
    public function terminateImport(bool $failed = false);

    /**
     * Check if the import has been terminated
     *
     * @return bool
     */
    public function isImportTerminated(): bool;

    /**
     * Check if import failed
     *
     * @return bool
     */
    public function isFailed(): bool;

    /**
     * @param array $rowNumbers
     * @return void
     */
    public function addSkippedRowNumbers(array $rowNumbers);

    /**
     * @return array
     */
    public function getSkippedRowNumbers(): array;

    /**
     * @param bool $hasNonEmptyData
     * @return void
     */
    public function setHasNonEmptyData(bool $hasNonEmptyData);

    /**
     * @return bool
     */
    public function hasNonEmptyData(): bool;

    /**
     * @param int $type
     * @param string $message
     * @return void
     */
    public function logMessage(int $type, $message);

    /**
     * @param string $message
     * @param int $rowNumber
     * @param string|null $entityName
     * @return void
     */
    public function logValidationMessage($message, int $rowNumber, ?string $entityName = null);

    /**
     * @return array
     */
    public function getMessages(): array;

    /**
     * @return array
     */
    public function getValidationMessages(): array;

    /**
     * @return array
     */
    public function getFilteringMessages(): array;

    /**
     * @return void
     */
    public function clearMessages();

    /**
     * @param int $records
     * @return void
     */
    public function setTotalRecords(int $records);

    /**
     * @return int
     */
    public function getTotalRecords(): int;

    /**
     * Adds behavior result to import result
     *
     * @param BehaviorResultInterface $result
     * @return void
     */
    public function addBehaviorResult(BehaviorResultInterface $result);

    /**
     * @param int $records
     * @return void
     */
    public function setRecordsProcessed(int $records);

    /**
     * @return int
     */
    public function getRecordsProcessed(): int;

    /**
     * @param int $records
     * @return void
     */
    public function setRecordsAdded(int $records);

    /**
     * @return int
     */
    public function getRecordsAdded(): int;

    /**
     * @param int $records
     * @return void
     */
    public function setRecordsUpdated(int $records);

    /**
     * @return int
     */
    public function getRecordsUpdated(): int;

    /**
     * @param int $records
     * @return void
     */
    public function setRecordsDeleted(int $records);

    /**
     * @return int
     */
    public function getRecordsDeleted(): int;

    /**
     * @return void
     */
    public function resetProcessedRecords();

    /**
     * @param string $stage
     * @return void
     */
    public function setStage(string $stage);

    /**
     * @return string
     */
    public function getStage(): string;
}
