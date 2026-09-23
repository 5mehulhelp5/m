<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api;

interface ExportResultInterface extends \Serializable
{
    public const MESSAGE_CRITICAL = 50;
    public const MESSAGE_ERROR = 40;
    public const MESSAGE_WARNING = 30;
    public const MESSAGE_INFO = 20;
    public const MESSAGE_DEBUG = 10;

    public const STAGE_INITIAL = 'initial';

    public function terminateExport(bool $failed = false);
    public function isExportTerminated(): bool;

    public function isFailed(): bool;

    public function logMessage(int $type, $message);

    public function getMessages(): array;
    public function clearMessages();

    public function setTotalRecords(int $records);
    public function getTotalRecords(): int;

    public function setRecordsProcessed(int $records);
    public function getRecordsProcessed(): int;

    public function getResultFileName(): ?string;
    public function setFilename(string $fileName): ExportResultInterface;
    public function getFilename(): ?string;
    public function setExtension(string $extension): ExportResultInterface;
    public function getExtension(): ?string;

    public function setStage(string $stage);
    public function getStage(): string;
}
