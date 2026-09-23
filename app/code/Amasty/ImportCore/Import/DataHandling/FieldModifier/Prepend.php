<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Api\Config\Profile\ModifierInterface;
use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;

class Prepend extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    public function __construct(
        $config,
        ArgumentConverter $argumentConverter
    ) {
        parent::__construct($config);
        $this->argumentConverter = $argumentConverter;
    }

    public function transform($value)
    {
        if (!isset($this->config['input_value'])) {
            return $value;
        }

        return $this->config['input_value'] . $value;
    }

    public function getValue(ModifierInterface $modifier): array
    {
        $modifierData = [];
        foreach ($modifier->getArguments() as $argument) {
            $modifierData['value'][$argument->getName()] = $argument->getValue();
        }

        $modifierData['select_value'] = $modifier->getModifierClass();

        return $modifierData;
    }

    public function prepareArguments(FieldInterface $field, $requestData): array
    {
        $arguments = [];
        if (!empty($requestData['value']['input_value'])) {
            $arguments = $this->argumentConverter->valueToArguments(
                (string)$requestData['value']['input_value'],
                'input_value',
                'string'
            );
        }

        return $arguments;
    }

    public function getGroup(): string
    {
        return ModifierProvider::TEXT_GROUP;
    }

    public function getLabel(): string
    {
        return __('Prepend')->getText();
    }

    public function getJsConfig(): array
    {
        return parent::getJsConfig() + [
            'childTemplate' => 'Amasty_ImportCore/fields/1input-modifier',
            'childComponent' => 'Amasty_ImportCore/js/fields/modifier-field'
        ];
    }
}
