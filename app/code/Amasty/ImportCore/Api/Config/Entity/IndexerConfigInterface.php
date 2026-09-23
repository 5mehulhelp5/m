<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity;

/**
 * Entity indexer config
 */
interface IndexerConfigInterface
{
    /**
     * @param string $class
     * @return void
     */
    public function setIndexerClass(string $class): void;

    /**
     * Get indexer instance
     *
     * @return mixed
     */
    public function getIndexer();

    /**
     * @param string $type
     * @return void
     */
    public function setApplyType(string $type): void;

    /**
     * @return string
     * @return void
     */
    public function getApplyType(): string;

    /**
     * Set indexer methods
     *
     * @param array $methods
     * @return void
     */
    public function setIndexerMethods(array $methods): void;

    /**
     * Get indexer class method by behavior code
     *
     * @param string $behaviorCode
     * @return string|null
     */
    public function getIndexerMethodByBehavior(string $behaviorCode): ?string;
}
