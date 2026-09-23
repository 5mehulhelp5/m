<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api;

/**
 * Import action interface
 */
interface ActionInterface
{
    /**
     * Initializes import action
     *
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    public function initialize(ImportProcessInterface $importProcess): void;

    /**
     * Performs import action
     *
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    public function execute(ImportProcessInterface $importProcess): void;
}
