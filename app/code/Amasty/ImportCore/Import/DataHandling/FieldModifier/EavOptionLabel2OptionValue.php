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
use Magento\Ui\Component\Form\Element\MultiSelect;

class EavOptionLabel2OptionValue extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array|null
     */
    private $map = null;

    /**
     * @var array
     */
    private $attributeOptions = [];

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    /**
     * @var OptionsProcessor
     */
    private $optionsProcessor;

    /**
     * @var array
     */
    private $allowedFrontendInput;

    public function __construct(
        $config,
        EavConfig $eavConfig,
        ArgumentConverter $argumentConverter,
        OptionsProcessor $optionsProcessor,
        array $allowedFrontendInput = []
    ) {
        parent::__construct($config);
        $this->eavConfig = $eavConfig;
        $this->argumentConverter = $argumentConverter;
        $this->optionsProcessor = $optionsProcessor;
        $this->allowedFrontendInput = $allowedFrontendInput;
    }

    public function transform($value)
    {
        $map = $this->getMap();
        $attribute = $this->getEavAttribute();
        if ($attribute
            && !empty($value)
            && in_array($attribute->getFrontendInput(), $this->allowedFrontendInput)
        ) {
            $multiSelectOptions = explode(',', (string)$value);
            $result = [];
            foreach ($multiSelectOptions as $option) {
                if (array_key_exists($option, $map)) {
                    $result[] = $map[$option];
                }
            }

            return implode(',', $result);
        }

        return $map[(string)$value] ?? $value;
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
            '20',
            'modifier_priority',
            'string'
        );

        return array_merge([], ...$arguments);
    }

    /**
     * Get option value to option label map
     *
     * @return array
     */
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

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Option Label To Option Value')->getText();
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

    protected function getEavAttribute(): ?AbstractAttribute
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
}
