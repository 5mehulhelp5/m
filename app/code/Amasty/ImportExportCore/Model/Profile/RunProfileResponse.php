<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\Profile;

use Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class RunProfileResponse extends AbstractSimpleObject implements RunProfileResponseInterface
{
    public function getStatusCode(): ?int
    {
        return $this->_get(self::STATUS_CODE) ? (int)$this->_get(self::STATUS_CODE) : null;
    }

    public function setStatusCode(?int $statusCode): void
    {
        $this->setData(self::STATUS_CODE, $statusCode);
    }

    public function getIdentity(): string
    {
        return (string)$this->_get(self::IDENTITY);
    }

    public function setIdentity(?string $identity): void
    {
        $this->setData(self::IDENTITY, $identity);
    }

    public function getMessage(): string
    {
        return $this->_get(self::MESSAGE);
    }

    public function setMessage(string $message): void
    {
        $this->setData(self::MESSAGE, $message);
    }
}
