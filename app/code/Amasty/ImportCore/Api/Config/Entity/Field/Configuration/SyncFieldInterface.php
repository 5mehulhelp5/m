<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity\Field\Configuration;

/**
 * Field value synchronization config
 */
interface SyncFieldInterface
{
    /**
     * @param string $entityName
     * @return void
     */
    public function setEntityName(string $entityName): void;

    /**
     * @return string
     */
    public function getEntityName(): string;

    /**
     * @param string $fieldName
     * @return void
     */
    public function setFieldName(string $fieldName): void;

    /**
     * @return string
     */
    public function getFieldName(): string;

    /**
     * @param string $fieldName
     * @return void
     */
    public function setSynchronizationFieldName(string $fieldName): void;

    /**
     * @return string
     */
    public function getSynchronizationFieldName(): string;
}
