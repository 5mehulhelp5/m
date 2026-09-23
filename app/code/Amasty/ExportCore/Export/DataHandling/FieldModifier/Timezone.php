<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier;

use Amasty\ExportCore\Api\Config\Profile\FieldInterface;
use Amasty\ExportCore\Api\Config\Profile\ModifierInterface;
use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Amasty\ExportCore\Export\SourceOption\TimezoneOffsetOptions;
use Amasty\ExportCore\Export\Utils\Config\ArgumentConverter;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

class Timezone extends AbstractModifier implements FieldModifierInterface
{
    public const UTC_0 = 0;

    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    /**
     * @var TimezoneOffsetOptions
     */
    private $timezoneOffsetOptions;

    public function __construct(
        $config,
        ArgumentConverter $argumentConverter,
        TimezoneOffsetOptions $timezoneOffsetOptions
    ) {
        parent::__construct($config);
        $this->argumentConverter = $argumentConverter;
        $this->timezoneOffsetOptions = $timezoneOffsetOptions;
    }

    public function transform($value)
    {
        $offset = (int)($this->config['input_value'] ?? self::UTC_0);

        if (empty($value)
            || strtotime($value) === false
            || $offset === self::UTC_0
            || !in_array($offset, $this->timezoneOffsetOptions->toArray())
        ) {
            return $value;
        }

        $datetime = new \DateTime();
        $timestamp = strtotime($value) + $offset;
        $datetime->setTimestamp($timestamp);

        return $datetime->format(Mysql::DATETIME_FORMAT);
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
                'number'
            );
        }

        return $arguments;
    }

    public function getGroup(): string
    {
        return ModifierProvider::DATE_GROUP;
    }

    public function getLabel(): string
    {
        return __('Apply Timezone')->render();
    }

    public function getJsConfig(): array
    {
        return [
            'component' => 'Amasty_ExportCore/js/fields/modifier',
            'template' => 'Amasty_ExportCore/fields/modifier',
            'childTemplate' => 'Amasty_ExportCore/fields/selectinput-modifier',
            'childComponent' => 'Amasty_ExportCore/js/fields/modifier-field',
            'additionalData' => ['options' => $this->timezoneOffsetOptions->toOptionArray()]
        ];
    }
}
