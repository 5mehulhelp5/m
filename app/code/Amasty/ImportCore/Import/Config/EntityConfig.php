<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config;

use Amasty\ImportCore\Api\Config\EntityConfigExtensionInterface;
use Amasty\ImportCore\Api\Config\EntityConfigExtensionInterfaceFactory;
use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Magento\Framework\DataObject;

class EntityConfig extends DataObject implements EntityConfigInterface
{
    public const ENTITY_CODE = 'entity_code';
    public const NAME = 'name';
    public const GROUP = 'group';
    public const DESCRIPTION = 'description';
    public const IS_HIDDEN_IN_LISTS = 'is_hidden_in_lists';
    public const ALLOW_EMPTY_IMPORT = 'allow_empty_import';
    public const BEHAVIORS = 'behaviors';
    public const INDEXER_CONFIG = 'indexer_config';
    public const FILE_UPLOADER_CONFIG = 'file_uploader_config';
    public const FIELDS_CONFIG = 'fields_config';
    public const EXTENSION_ATTRIBUTES = 'extension_attributes';

    /**
     * @var EntityConfigExtensionInterfaceFactory
     */
    private $extensionAttributesFactory;

    public function __construct(
        EntityConfigExtensionInterfaceFactory $extensionAttributesFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    public function getEntityCode()
    {
        return $this->getData(self::ENTITY_CODE);
    }

    public function setEntityCode($entityCode)
    {
        $this->setData(self::ENTITY_CODE, $entityCode);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

    public function getGroup()
    {
        return $this->getData(self::GROUP);
    }

    public function setGroup($group)
    {
        $this->setData(self::GROUP, $group);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    public function isHiddenInLists()
    {
        return $this->getData(self::IS_HIDDEN_IN_LISTS);
    }

    public function setHiddenInLists($isHiddenInLists)
    {
        $this->setData(self::IS_HIDDEN_IN_LISTS, $isHiddenInLists);
    }

    public function isAllowEmptyImport()
    {
        return (bool)$this->getData(self::ALLOW_EMPTY_IMPORT);
    }

    public function setAllowEmptyImport($isAllow)
    {
        $this->setData(self::ALLOW_EMPTY_IMPORT, $isAllow);
    }

    public function getBehaviors()
    {
        return $this->getData(self::BEHAVIORS);
    }

    public function setBehaviors($behaviors)
    {
        $this->setData(self::BEHAVIORS, $behaviors);
    }

    public function getIndexerConfig()
    {
        return $this->getData(self::INDEXER_CONFIG);
    }

    public function setIndexerConfig($indexerConfig)
    {
        $this->setData(self::INDEXER_CONFIG, $indexerConfig);
    }

    public function getFileUploaderConfig()
    {
        return $this->getData(self::FILE_UPLOADER_CONFIG);
    }

    public function setFileUploaderConfig($fileUploaderConfig)
    {
        $this->setData(self::FILE_UPLOADER_CONFIG, $fileUploaderConfig);
    }

    public function getFieldsConfig()
    {
        return $this->getData(self::FIELDS_CONFIG);
    }

    public function setFieldsConfig($fieldsConfig)
    {
        $this->setData(self::FIELDS_CONFIG, $fieldsConfig);
    }

    public function getExtensionAttributes(): EntityConfigExtensionInterface
    {
        if (!$this->hasData(self::EXTENSION_ATTRIBUTES)) {
            $this->setExtensionAttributes($this->extensionAttributesFactory->create());
        }

        return $this->getData(self::EXTENSION_ATTRIBUTES);
    }

    public function setExtensionAttributes(
        EntityConfigExtensionInterface $extensionAttributes
    ): void {
        $this->setData(self::EXTENSION_ATTRIBUTES, $extensionAttributes);
    }
}
