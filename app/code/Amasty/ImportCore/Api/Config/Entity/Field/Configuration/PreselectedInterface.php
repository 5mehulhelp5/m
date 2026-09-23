<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity\Field\Configuration;

/**
 * Entity field preselection config
 */
interface PreselectedInterface
{
    /**
     * Sets required flag. This flag determines whether the field is required or not
     * for certain behaviors
     *
     * @param bool $isRequired
     * @return void
     */
    public function setIsRequired(bool $isRequired): void;

    /**
     * Get required flag
     *
     * @return bool
     */
    public function getIsRequired(): bool;

    /**
     * @param array $includeBehaviors
     * @return void
     */
    public function setIncludeBehaviors(array $includeBehaviors): void;

    /**
     * @return array|null
     */
    public function getIncludeBehaviors(): ?array;

    /**
     * @param array $excludeBehaviors
     * @return void
     */
    public function setExcludeBehaviors(array $excludeBehaviors): void;

    /**
     * @return array|null
     */
    public function getExcludeBehaviors(): ?array;
}
