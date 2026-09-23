<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config;

class DemoEntitiesPool
{
    /**
     * @var DemoEntityConfig[]
     */
    private $demoEntities;

    public function __construct(
        array $demoEntities = []
    ) {
        $this->initializeDemoEntities($demoEntities);
    }

    /**
     * @return DemoEntityConfig[]
     */
    public function getDemoEntities(): array
    {
        return $this->demoEntities;
    }

    private function initializeDemoEntities(array $demoEntities): void
    {
        foreach ($demoEntities as $key => $entity) {
            if (!$entity instanceof DemoEntityConfig) {
                throw new \LogicException(
                    sprintf('Demo Entity must implement %s', DemoEntityConfig::class)
                );
            }
        }
        $this->demoEntities = $demoEntities;
    }
}
