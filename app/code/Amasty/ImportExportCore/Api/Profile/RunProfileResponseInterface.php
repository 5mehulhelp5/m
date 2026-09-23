<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Profile;

interface RunProfileResponseInterface
{
    public const STATUS_CODE = 'status_code';
    public const IDENTITY = 'identity';
    public const MESSAGE = 'message';

    /**
     * @return int|null
     */
    public function getStatusCode(): ?int;

    /**
     * @param int|null $statusCode
     * @return void
     */
    public function setStatusCode(?int $statusCode): void;

    /**
     * @return string
     */
    public function getIdentity(): string;

    /**
     * @param string|null $identity
     * @return void
     */
    public function setIdentity(?string $identity): void;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void;
}
