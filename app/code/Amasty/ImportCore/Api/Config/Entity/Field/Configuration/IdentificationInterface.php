<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity\Field\Configuration;

/**
 * Entity field identification config
 */
interface IdentificationInterface
{
    /**
     * Sets identifier flag. This flag determines whether the field is identity field or not
     *
     * @param bool $isIdentifier
     * @return void
     */
    public function setIsIdentifier(bool $isIdentifier): void;

    /**
     * Get identifier flag
     *
     * @return bool
     */
    public function isIdentifier(): bool;

    /**
     * Set identifier label. This label used in UI for rendering corresponding select option
     *
     * @param string $label
     * @return void
     */
    public function setLabel(string $label): void;

    /**
     * Get identifier label
     *
     * @return string
     */
    public function getLabel(): string;
}
