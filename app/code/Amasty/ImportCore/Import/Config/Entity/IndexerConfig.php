<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Entity;

use Amasty\ImportCore\Api\Config\Entity\IndexerConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

class IndexerConfig extends DataObject implements IndexerConfigInterface
{
    public const INDEXER_CLASS = 'class';
    public const APPLY_TYPE = 'apply_type';
    public const INDEXER_METHODS = 'indexer_methods';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var mixed
     */
    private $indexer = null;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($data);
        $this->objectManager = $objectManager;
    }

    public function setIndexerClass(string $class): void
    {
        $this->setData(self::INDEXER_CLASS, $class);
    }

    public function getIndexer()
    {
        if (!$this->indexer) {
            $this->indexer = $this->objectManager->get($this->getData(self::INDEXER_CLASS));
        }

        return $this->indexer;
    }

    public function setApplyType(string $type): void
    {
        $this->setData(self::APPLY_TYPE, $type);
    }

    public function getApplyType(): string
    {
        return $this->getData(self::APPLY_TYPE);
    }

    public function setIndexerMethods(array $methods): void
    {
        $this->setData(self::INDEXER_METHODS, $methods);
    }

    public function getIndexerMethodByBehavior(string $behaviorCode): ?string
    {
        return $this->getData(self::INDEXER_METHODS)[$behaviorCode] ?? null;
    }
}
