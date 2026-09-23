<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Config\OptionSource;

use Amasty\ExportCore\Export\Config\DemoEntitiesPool;
use Amasty\ExportCore\Export\Config\DemoEntityConfig;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;

class Entities implements OptionSourceInterface
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var DemoEntitiesPool
     */
    private $demoEntitiesPool;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        ?DemoEntitiesPool $demoEntitiesPool = null
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->demoEntitiesPool = $demoEntitiesPool ?? ObjectManager::getInstance()->get(DemoEntitiesPool::class);
    }

    public function toOptionArray()
    {
        $result = [];
        $entitiesConfig = $this->entityConfigProvider->getConfig();
        $demoMap = $this->getDemoEntitiesMap();
        foreach ($entitiesConfig as $entity) {
            if ($entity->isHiddenInLists()) {
                continue;
            }
            if ($entity->getGroup()) {
                $groupKey = hash('ripemd160', $entity->getGroup());
                if (!isset($result[$groupKey])) {
                    $result[$groupKey] = ['label' => $entity->getGroup(), 'optgroup' => [], 'value' => $groupKey];
                }
                $result[$groupKey]['optgroup'][] = ['label' => $entity->getName(), 'value' => $entity->getEntityCode()];
            } else {
                $result[] = ['label' => $entity->getName(), 'value' => $entity->getEntityCode()];
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
                $groupKey = hash('ripemd160', $entity->getGroup());
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
