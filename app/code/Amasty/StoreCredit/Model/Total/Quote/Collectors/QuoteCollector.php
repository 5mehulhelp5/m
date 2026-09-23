<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote\Collectors;

class QuoteCollector
{
    /**
     * @var QuoteCollectorInterface[]
     */
    private $collectors;

    /**
     * @var StorefrontCollector
     */
    private $defaultCollector;

    public function __construct(StorefrontCollector $defaultCollector, array $collectors = [])
    {
        $this->defaultCollector = $defaultCollector;
        $this->setCollectors($collectors);
    }

    /**
     * @param string $area
     * @return QuoteCollectorInterface
     */
    public function get(string $area): QuoteCollectorInterface
    {
        return $this->collectors[$area] ?? $this->defaultCollector;
    }

    /**
     * @param array $collectors
     */
    private function setCollectors(array $collectors): void
    {
        foreach ($collectors as $collector) {
            if (!$collector instanceof QuoteCollectorInterface) {
                throw new \InvalidArgumentException(
                    'Type "' . get_class($collector) . '" is not instance on ' . QuoteCollectorInterface::class
                );
            }
        }
        $this->collectors = $collectors;
    }
}
