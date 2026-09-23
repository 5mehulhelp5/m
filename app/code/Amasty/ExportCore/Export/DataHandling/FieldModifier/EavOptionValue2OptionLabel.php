<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier;

use Amasty\ExportCore\Api\Config\Profile\FieldInterface;
use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ActionConfigBuilder;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Amasty\ExportCore\Export\Utils\Config\ArgumentConverter;
use Amasty\ImportExportCore\Utils\OptionsProcessor;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\LocalizedException;

class EavOptionValue2OptionLabel extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array|null
     */
    private $map;

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
        $this->optionsProcessor = $optionsProcessor;
        $this->argumentConverter = $argumentConverter;
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
            $multiSelectOptions = explode(',', $value);
            $result = [];
            foreach ($multiSelectOptions as $option) {
                if (array_key_exists($option, $map)) {
                    $result[] = $map[$option];
                }
            }

            return implode(',', $result);
        } else {
            return $map[$value] ?? $value;
        }
    }

    public function prepareArguments(FieldInterface $field, $requestData): array
    {
        $arguments = [];
        if ($eavEntityType = $requestData[ActionConfigBuilder::EAV_ENTITY_TYPE_CODE] ?? null) {
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

        return array_merge([], ...$arguments);
    }

    /**
     * Get option value to option label map
     *
     * @return array
     */
    private function getMap()
    {
        if (!$this->map) {
            $this->map = [];

            $attribute = $this->getEavAttribute();
            if (!$attribute) {
                return $this->map;
            }
            $options = $this->getAttributeOptions($attribute);
            foreach ($options as $option) {
                // Skip ' -- Please Select -- ' option
                if (strlen((string)$option['value'])) {
                    $this->map[$option['value']] = $option['label'];
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
        return __('Option Value To Option Label')->getText();
    }

    private function getAttributeOptions($attribute): array
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!isset($this->attributeOptions[$attributeCode])) {
            $this->attributeOptions[$attributeCode] = $this->optionsProcessor->process(
                $attribute->getSource()->getAllOptions(),
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
