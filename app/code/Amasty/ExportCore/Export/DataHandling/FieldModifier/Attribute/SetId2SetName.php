<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\Attribute;

use Amasty\ExportCore\Api\Config\Profile\FieldInterface;
use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ActionConfigBuilder;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Amasty\ExportCore\Export\Utils\Config\ArgumentConverter;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

class SetId2SetName extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var string[]
     */
    private $map;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    public function __construct(
        $config,
        Config $eavConfig,
        CollectionFactory $collectionFactory,
        ArgumentConverter $argumentConverter
    ) {
        parent::__construct($config);
        $this->eavConfig = $eavConfig;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->argumentConverter = $argumentConverter;
    }

    public function transform($value)
    {
        return $this->getMap()[$value] ?? $value;
    }

    public function prepareArguments(FieldInterface $field, $requestData): array
    {
        $arguments = [];
        if ($entityType = $requestData[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE] ?? null) {
            $arguments = $this->argumentConverter->valueToArguments(
                (string)$entityType,
                ActionConfigBuilder::EAV_ENTITY_TYPE_CODE,
                'string'
            );
        }

        return $arguments;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Attribute Set ID to Set Name')->render();
    }

    private function getMap(): array
    {
        if ($this->map === null) {
            $collection = $this->collectionFactory->create();
            if (isset($this->config[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE])) {
                $entityType = $this->eavConfig->getEntityType(
                    $this->config[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE]
                );
                $collection->setEntityTypeFilter((int)$entityType->getId());
            }
            $this->map = $collection->toOptionHash();
        }

        return $this->map;
    }
}
