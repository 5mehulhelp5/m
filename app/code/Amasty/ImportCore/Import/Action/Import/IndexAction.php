<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\Import;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\Config\Entity\IndexerConfigInterface;
use Amasty\ImportCore\Api\Config\Profile\EntitiesConfigInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;

class IndexAction implements ActionInterface
{
    public const TYPE_BATCH = 'batch';
    public const TYPE_ENTITY = 'entity';

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    public function __construct(
        EntityConfigProvider $entityConfigProvider
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function initialize(ImportProcessInterface $importProcess): void
    {
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        $entityResults = (array)$importProcess->getProcessedEntityResult();
        if (!$entityResults) {
            return;
        }

        foreach ($entityResults as $entityCode => $entityResult) {
            $entityConfig = $this->entityConfigProvider->getConfig($entityCode);

            if (!$entityConfig->getIndexerConfig()) {
                continue;
            }

            $behaviorCode = $this->getBehaviorCodeByEntityCode(
                $entityCode,
                $importProcess->getProfileConfig()->getEntitiesConfig()
            );
            if (!$behaviorCode) {
                continue;
            }

            $this->executeIndexer(
                $entityConfig->getIndexerConfig(),
                $behaviorCode,
                $entityResult->getAffectedIds()
            );
        }
    }

    private function getBehaviorCodeByEntityCode(
        string $entityCode,
        EntitiesConfigInterface $entitiesConfig
    ): ?string {
        if ($entitiesConfig->getEntityCode() == $entityCode) {
            return $entitiesConfig->getBehavior();
        }

        foreach ($entitiesConfig->getSubEntitiesConfig() as $subEntityConfig) {
            $behaviorCode = $this->getBehaviorCodeByEntityCode($entityCode, $subEntityConfig);
            if ($behaviorCode) {
                return $behaviorCode;
            }
        }

        return null;
    }

    private function executeIndexer(
        IndexerConfigInterface $indexerConfig,
        string $behaviorCode,
        array $ids
    ): void {
        if (!$indexerConfig->getIndexer() || !$indexerConfig->getIndexerMethodByBehavior($behaviorCode)) {
            return;
        }
        $method = $indexerConfig->getIndexerMethodByBehavior($behaviorCode);

        switch ($indexerConfig->getApplyType()) {
            case self::TYPE_BATCH:
                $indexerConfig->getIndexer()->$method($ids);
                break;
            case self::TYPE_ENTITY:
                foreach ($ids as $entityId) {
                    $indexerConfig->getIndexer()->$method($entityId);
                }
                break;
        }
    }
}
