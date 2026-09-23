<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ActionConfigBuilder;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;
use Amasty\ImportExportCore\Utils\OptionsProcessor;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Store\Model\Store;

class CreateAttributeValue extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var array
     */
    private $attributeOptions = [];

    /**
     * @var array|null
     */
    private $map = null;

    /**
     * @var array
     */
    private $multiSelectInput;

    /**
     * @var array
     */
    private $disallowedFrontendInput;

    /**
     * @var OptionsProcessor
     */
    private $optionsProcessor;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    private $optionLabelFactory;

    /**
     * @var AttributeOptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var AttributeOptionManagementInterface
     */
    private $attributeOptionManagement;

    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    public function __construct(
        $config,
        OptionsProcessor $optionsProcessor,
        EavConfig $eavConfig,
        AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        AttributeOptionInterfaceFactory $optionFactory,
        AttributeOptionManagementInterface $attributeOptionManagement,
        ArgumentConverter $argumentConverter,
        array $multiSelectInput = [],
        array $disallowedFrontendInput = []
    ) {
        parent::__construct($config);
        $this->optionsProcessor = $optionsProcessor;
        $this->eavConfig = $eavConfig;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->argumentConverter = $argumentConverter;
        $this->multiSelectInput = $multiSelectInput;
        $this->disallowedFrontendInput = $disallowedFrontendInput;
    }

    public function getLabel(): string
    {
        return __('Create New Attribute Value')->getText();
    }

    public function transform($value)
    {
        $map = $this->getMap();
        if (!empty($value)
            && ($attribute = $this->getEavAttribute())
            && in_array($attribute->getFrontendInput(), $this->multiSelectInput)
        ) {
            $multiSelectOptions = explode(',', (string)$value);
            $result = [];
            foreach ($multiSelectOptions as $option) {
                if (array_key_exists($option, $map)) {
                    $result[$map[$option]] = $option;
                } elseif ($newOption = $this->createNewOption($attribute, $option)) {
                    $result[$newOption] = $option;
                    $this->map[$option] = $newOption;
                }
            }

            return implode(',', $result);
        }

        if (!isset($map[$value])
            && $value
            && ($attribute = $this->getEavAttribute())
            && !in_array($attribute->getFrontendInput(), $this->disallowedFrontendInput)
        ) {
            $newOption = $this->createNewOption($attribute, $value);
            $this->map[$value] = $newOption;
        }

        return $value;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function prepareArguments(FieldInterface $field, $requestData): array
    {
        $arguments = [];
        $eavEntityType = $requestData[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE] ?? null;
        if ($eavEntityType) {
            $arguments[] = $this->argumentConverter->valueToArguments(
                (string)$eavEntityType,
                ActionConfigBuilder::EAV_ENTITY_TYPE_CODE,
                'string'
            );
        }

        $arguments[] = $this->argumentConverter->valueToArguments(
            $field->getName(),
            'field',
            'string'
        );
        $arguments[] = $this->argumentConverter->valueToArguments(
            '10',
            'modifier_priority',
            'string'
        );

        return array_merge([], ...$arguments);
    }

    private function getMap(): array
    {
        if ($this->map === null) {
            $this->map = [];
            $attribute = $this->getEavAttribute();
            if (!$attribute) {
                return $this->map;
            }

            $options = $this->getAttributeOptions($attribute);
            foreach ($options as $option) {
                // Skip ' -- Please Select -- ' option
                if (strlen((string)$option['value'])) {
                    $label = $option['label'] instanceof Phrase
                        ? $option['label']->getText()
                        : $option['label'];
                    $this->map[$label] = $option['value'];
                }
            }
        }

        return $this->map;
    }

    private function getAttributeOptions($attribute): array
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!isset($this->attributeOptions[$attributeCode])) {
            $allOptions = array_merge(
                $attribute->getSource()->getAllOptions(true, true), //adminhtml store options
                $attribute->getSource()->getAllOptions() //default store options
            );
            $this->attributeOptions[$attributeCode] = $this->optionsProcessor->process(
                $allOptions,
                false
            );
        }

        return $this->attributeOptions[$attributeCode];
    }

    private function getEavAttribute(): ?AbstractAttribute
    {
        $attribute = null;
        if (isset($this->config[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE], $this->config['field'])) {
            try {
                $attribute = $this->eavConfig->getAttribute(
                    $this->config[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE],
                    $this->config['field']
                );
            } catch (LocalizedException $e) {
                return null;
            }
        }

        return $attribute;
    }

    private function createNewOption(AbstractAttribute $attribute, string $value): ?string
    {
        $entityType = $attribute->getEntityType()->getEntityTypeCode();
        if (null === $entityType) {
            return null;
        }

        $optionLabel = $this->optionLabelFactory->create();
        $optionLabel->setStoreId(Store::DEFAULT_STORE_ID);
        $optionLabel->setLabel($value);

        $option = $this->optionFactory->create();
        $option->setLabel($optionLabel->getLabel());
        $option->setStoreLabels([$optionLabel]);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        return $this->attributeOptionManagement->add(
            $entityType,
            $attribute->getAttributeId(),
            $option
        );
    }
}
