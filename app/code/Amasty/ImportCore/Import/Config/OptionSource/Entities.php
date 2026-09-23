<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\OptionSource;

use Amasty\ImportCore\Import\Config\DemoEntitiesPool;
use Amasty\ImportCore\Import\Config\DemoEntityConfig;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Utils\Hash;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;

class Entities implements OptionSourceInterface
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var DemoEntitiesPool
     */
    private $demoEntitiesPool;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        Hash $hash,
        ?DemoEntitiesPool $demoEntitiesPool = null
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->hash = $hash;
        $this->demoEntitiesPool = $demoEntitiesPool ?? ObjectManager::getInstance()->get(DemoEntitiesPool::class);
    }

    public function toOptionArray(): array
    {
        $result = [];
        $entitiesConfig = $this->entityConfigProvider->getConfig();
        $demoMap = $this->getDemoEntitiesMap();
        foreach ($entitiesConfig as $entity) {
            if ($entity->isHiddenInLists()) {
                continue;
            }
            if ($entity->getGroup()) {
                $groupKey = $this->hash->hash($entity->getGroup());
                if (!isset($result[$groupKey])) {
                    $result[$groupKey] = [
                        'label' => $entity->getGroup(),
                        'optgroup' => [],
                        'value' => $groupKey
                    ];
                }
                $result[$groupKey]['optgroup'][] = [
                    'label' => $entity->getName(),
                    'value' => $entity->getEntityCode()
                ];
            } else {
                $result[] = [
                    'label' => $entity->getName(),
                    'value' => $entity->getEntityCode()
                ];
            }
            if (array_key_exists($entity->getEntityCode(), $demoMap)) {
                unset($demoMap[$entity->getEntityCode()]);
            }
        }

        $this->processDemoEntities($demoMap, $result);

        return array_values($result);
    }

    /**
     * @return DemoEntityConfig[]
     */
    private function getDemoEntitiesMap(): array
    {
        $demoMap = [];
        foreach ($this->demoEntitiesPool->getDemoEntities() as $entity) {
            $demoMap[$entity->getEntityCode()] = $entity;
        }

        return $demoMap;
    }

    private function processDemoEntities(array $demoMap, array &$result): void
    {
        foreach ($demoMap as $entity) {
            if ($entity->isHiddenInLists()) {
                continue;
            }
            if ($entity->getGroup()) {
                $groupKey = $this->hash->hash($entity->getGroup());
                if (!isset($result[$groupKey])) {
                    $result[$groupKey] = ['label' => $entity->getGroup(), 'optgroup' => [], 'value' => $groupKey];
                }

                $result[$groupKey]['optgroup'][] = $this->convertInOption($entity);
            } else {
                $result[] = $this->convertInOption($entity);
            }
        }
    }

    private function convertInOption(DemoEntityConfig $entity): array
    {
        $option = [
            'label' => $entity->getName(),
            'value' => $entity->getEntityCode(),
            'isPromo' => true
        ];

        return array_merge($option, $entity->getData('additional_data') ?: []);
    }
}
