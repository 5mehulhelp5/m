<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote\MaxStoreCredit;

use InvalidArgumentException;

class RetrieveStrategyPool
{
    public const DEFAULT_CODE = 0;

    /**
     * @var RetrieveStrategyInterface[]
     */
    private $retrievers;

    /**
     * @var FullStrategy
     */
    private $defaultStrategy;

    public function __construct(FullStrategy $defaultStrategy, array $retrievers = [])
    {
        $this->defaultStrategy = $defaultStrategy;
        $this->setRetrievers($retrievers);
    }

    /**
     * @param int $type
     * @return RetrieveStrategyInterface
     */
    public function get(int $type): RetrieveStrategyInterface
    {
        return $this->retrievers[$type] ?? $this->defaultStrategy;
    }

    private function setRetrievers(array $retrievers): void
    {
        foreach ($retrievers as $retriever) {
            if (!$retriever instanceof RetrieveStrategyInterface) {
                throw new InvalidArgumentException(
                    sprintf('Retrieve strategy must implement %s', RetrieveStrategyInterface::class)
                );
            }
        }
        $this->retrievers = $retrievers;
    }
}
