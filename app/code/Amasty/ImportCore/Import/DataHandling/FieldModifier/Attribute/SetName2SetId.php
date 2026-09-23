<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier\Attribute;

use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;
use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ActionConfigBuilder;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

class SetName2SetId extends AbstractModifier implements FieldModifierInterface
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
        $this->argumentConverter = $argumentConverter;
    }

    public function transform($value)
    {
        $map = $this->getMap();

        $result = null;
        if (isset($map[$value])) {
            $result = $map[$value];
        } elseif (is_numeric($value)) {
            $result = $value;
        }

        return $result;
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
        return __('Attribute Set Name to Set ID')->getText();
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
            } else {
                $r = 2;
            }
            $this->map = array_flip($collection->toOptionHash());
        }

        return $this->map;
    }
}
