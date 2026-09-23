<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\Process;

use Amasty\ImportExportCore\Api\Process\ProcessStatusInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class ProcessStatus extends AbstractSimpleObject implements ProcessStatusInterface
{
    public const STATUS = 'status';
    public const PROCEED = 'proceed';
    public const TOTAL = 'total';
    public const MESSAGES = 'messages';
    public const ERROR = 'error';

    public function getStatus(): string
    {
        return (string)$this->_get(self::STATUS);
    }

    public function setStatus(string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    public function getProceed(): int
    {
        return (int)$this->_get(self::PROCEED);
    }

    public function setProceed(int $proceed): void
    {
        $this->setData(self::PROCEED, $proceed);
    }

    public function getTotal(): int
    {
        return (int)$this->_get(self::TOTAL);
    }

    public function setTotal(int $total): void
    {
        $this->setData(self::TOTAL, $total);
    }

    public function getMessages(): array
    {
        return (array)$this->_get(self::MESSAGES);
    }

    public function setMessages(array $messages): void
    {
        $this->setData(self::MESSAGES, $messages);
    }

    public function getError(): string
    {
        return (string)$this->_get(self::ERROR);
    }

    public function setError(string $error): void
    {
        $this->setData(self::ERROR, $error);
    }
}
