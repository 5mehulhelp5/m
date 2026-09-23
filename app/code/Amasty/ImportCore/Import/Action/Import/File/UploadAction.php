<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\Import\File;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\Action\FileUploaderInterface;
use Amasty\ImportCore\Api\Config\Entity\FileUploaderConfigInterface;
use Amasty\ImportCore\Api\Config\Profile\EntitiesConfigInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Config\RelationConfigProvider;
use Magento\Framework\App\ObjectManager;

class UploadAction implements ActionInterface
{
    /**
     * @var EntityConfigProvider
     */
    private EntityConfigProvider $entityConfigProvider;

    /**
     * @var RelationConfigProvider
     */
    private RelationConfigProvider $relationConfigProvider;

    /**
     * @var FileUploaderInterface[]
     */
    private array $uploaderStorage = [];

    public function __construct(
        ?EntityConfigProvider $entityConfigProvider = null,
        ?RelationConfigProvider $relationConfigProvider = null
    ) {
        $this->entityConfigProvider = $entityConfigProvider
            ?: ObjectManager::getInstance()->get(EntityConfigProvider::class);
        $this->relationConfigProvider = $relationConfigProvider
            ?: ObjectManager::getInstance()->get(RelationConfigProvider::class);
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        $entitiesConfig = $importProcess->getProfileConfig()->getEntitiesConfig();
        $this->initFileUploaderStorage($entitiesConfig);
        foreach ($this->uploaderStorage as $fileUploader) {
            $fileUploader->initialize($importProcess);
        }
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        foreach ($this->uploaderStorage as $fileUploader) {
            $fileUploader->execute($importProcess);
        }
    }

    private function initFileUploaderStorage(EntitiesConfigInterface $entitiesConfig): void
    {
        $entityCode = $entitiesConfig->getEntityCode();
        $fileUploaderConfig = $this->entityConfigProvider->get($entityCode)->getFileUploaderConfig();
        if ($fileUploaderConfig) {
            $this->prepareFileUploader($fileUploaderConfig);
        }
        $relationsConfig = $this->getRelationsConfigByEntityCode($entityCode);
        $subEntitiesConfig = $entitiesConfig->getSubEntitiesConfig();
        foreach ($subEntitiesConfig as $subEntityConfig) {
            if (isset($relationsConfig[$subEntityConfig->getEntityCode()])) {
                $this->initFileUploaderStorage($subEntityConfig);
            }
        }
    }

    private function getRelationsConfigByEntityCode(string $entityCode): array
    {
        $relationsConfig = [];
        foreach ($this->relationConfigProvider->get($entityCode) as $relation) {
            $relationsConfig[$relation->getChildEntityCode()] = $relation;
        }

        return $relationsConfig;
    }

    private function prepareFileUploader(FileUploaderConfigInterface $fileUploaderConfig): void
    {
        $fileUploader = $fileUploaderConfig->getFileUploader();
        if (!$fileUploader instanceof FileUploaderInterface) {
            $fileUploaderClass = $fileUploaderConfig->getFileUploaderClass();
            throw new \RuntimeException(
                'Class ' . $fileUploaderClass . ' doesn\'t implement ' . FileUploaderInterface::class
            );
        }
        $this->uploaderStorage[] = $fileUploader;
    }
}
