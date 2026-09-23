<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Process;

interface ProcessStatusInterface
{
    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getProceed(): int;

    public function setProceed(int $proceed): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getMessages(): array;

    public function setMessages(array $messages): void;

    public function getError(): string;

    public function setError(string $error): void;
}
