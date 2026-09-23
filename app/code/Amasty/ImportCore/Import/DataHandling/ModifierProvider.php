<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterfaceFactory;
use Amasty\ImportExportCore\Config\ConfigClass\Factory;

class ModifierProvider
{
    public const TEXT_GROUP = 'text';
    public const NUMERIC_GROUP = 'numeric';
    public const DATE_GROUP = 'date';
    public const CUSTOM_GROUP = 'custom';

    /**
     * @var string[]
     */
    private $defaultModifiers = [
        // text
        FieldModifier\Append::class,
        FieldModifier\Prepend::class,
        FieldModifier\Trim::class,
        FieldModifier\Uppercase::class,
        FieldModifier\Lowercase::class,
        FieldModifier\Capitalize::class,
        FieldModifier\CapitalizeEachWord::class,
        FieldModifier\Strip::class,
        FieldModifier\Replace::class,
        FieldModifier\FullMatchReplace::class,
        FieldModifier\ReplaceFirst::class,

        // numeric
        FieldModifier\Number\Absolute::class,
        FieldModifier\Number\Round::class,
        FieldModifier\Number\Plus::class,
        FieldModifier\Number\Minus::class,
        FieldModifier\Number\Multiple::class,
        FieldModifier\Number\Divide::class,
        FieldModifier\Number\Modulo::class,
        FieldModifier\Number\Truncate::class,
        FieldModifier\Number\Ceil::class,
        FieldModifier\Number\Floor::class,

        // date
        FieldModifier\Date::class
    ];

    /**
     * @var ConfigClassInterfaceFactory
     */
    private $configClassFactory;

    /**
     * @var Factory
     */
    private $factory;

    public function __construct(
        ConfigClassInterfaceFactory $configClassFactory,
        Factory $factory
    ) {
        $this->configClassFactory = $configClassFactory;
        $this->factory = $factory;
    }

    public function getAllModifiers(EntityConfigInterface $entityConfig): array
    {
        $modifiers = $this->getPreparedDefaultModifiers();

        foreach ($entityConfig->getFieldsConfig()->getFields() as $fieldConfig) {
            if ($fieldConfig->getActions()) {
                foreach ($fieldConfig->getActions() as $action) {
                    $className = $action->getConfigClass()->getName();
                    if ($action->getConfigClass() && !isset($modifiers[$className])) {
                        $modifiers[$className] = [
                            'baseType'  => FieldModifierInterface::class,
                            'name' => $className,
                            'arguments' => $action->getConfigClass()->getArguments()
                        ];
                    }
                }
            }
        }

        return $modifiers;
    }

    public function getPreparedDefaultModifiers(): array
    {
        $modifiers = [];
        foreach ($this->defaultModifiers as $defaultModifier) {
            $modifiers[$defaultModifier] = [
                'baseType'  => FieldModifierInterface::class,
                'name' => $defaultModifier,
                'arguments' => []
            ];
        }

        return $modifiers;
    }

    public function getAllModifiersByGroups(EntityConfigInterface $entityConfig, string $fieldName): array
    {
        return array_merge(
            $this->getDefaultModifiersByGroups(),
            [$this->getEntityModifiersByGroups($entityConfig, $fieldName)]
        );
    }

    public function getDefaultModifiersByGroups(): array
    {
        $defaultModifiers = [];
        foreach ($this->defaultModifiers as $modifier) {
            $modifierObject = $this->getModifierObject($modifier);
            $modifierGroup = $modifierObject->getGroup();
            if (!isset($defaultModifiers[$modifierGroup]['value'])) {
                $defaultModifiers[$modifierGroup] = [
                    'label' => $this->getGroupLabel($modifierGroup),
                    'type'  => $modifierGroup,
                    'value' => []
                ];
            }

            $valueLabelArray = [
                'label' => $modifierObject->getLabel(),
                'value' => $modifier
            ];
            array_push($defaultModifiers[$modifierGroup]['value'], $valueLabelArray);
        }

        return array_values($defaultModifiers);
    }

    private function getEntityModifiersByGroups(
        EntityConfigInterface $entityConfig,
        string $fieldName
    ): array {
        $entityModifiers = $this->getEntityFieldModifiers($entityConfig, $fieldName);

        return [
            'label' => __('Custom Modifiers')->getText(),
            'type'  => self::CUSTOM_GROUP,
            'value' => array_values(array_unique($entityModifiers, SORT_REGULAR))
        ];
    }

    public function getEntityFieldModifiers(EntityConfigInterface $entityConfig, string $fieldName): array
    {
        $entityFieldModifiers = [];
        foreach ($entityConfig->getFieldsConfig()->getFields() as $fieldConfig) {
            if ($fieldConfig->getName() === $fieldName && !empty($fieldConfig->getActions())) {
                foreach ($fieldConfig->getActions() as $action) {
                    if (!$action->getConfigClass()
                        || $this->isSystemAction($action->getConfigClass()->getArguments())
                    ) {
                        continue;
                    }

                    $modifierObject = $this->getModifierObject(
                        $action->getConfigClass()->getName(),
                        $action->getConfigClass()->getArguments()
                    );
                    $entityFieldModifiers[] = [
                        'label' => $modifierObject->getLabel(),
                        'value' => $action->getConfigClass()->getName(),
                        'eavEntityType' => $this->findArgumentByName(
                            $action->getConfigClass()->getArguments(),
                            ActionConfigBuilder::EAV_ENTITY_TYPE_CODE
                        ),
                        'optionSource' => $this->findArgumentByName(
                            $action->getConfigClass()->getArguments(),
                            ActionConfigBuilder::OPTION_SOURCE
                        ),
                        'identity' => $this->findArgumentByName(
                            $action->getConfigClass()->getArguments(),
                            ActionConfigBuilder::IDENTITY
                        )
                    ];
                }
            }
        }

        return $entityFieldModifiers;
    }

    public function getEntityFieldModifiersValue(
        EntityConfigInterface $entityConfig,
        string $fieldName
    ): array {
        $entityFieldModifiers = [];
        foreach ($entityConfig->getFieldsConfig()->getFields() as $fieldConfig) {
            if ($fieldConfig->getName() === $fieldName && !empty($fieldConfig->getActions())) {
                foreach ($fieldConfig->getActions() as $action) {
                    if (!$action->getConfigClass()
                        || !$this->isPreselected($action->getConfigClass()->getArguments())
                    ) {
                        continue;
                    }

                    $entityFieldModifiers[] = [
                        'select_value'    => $action->getConfigClass()->getName(),
                        'eavEntityType' => $this->findArgumentByName(
                            $action->getConfigClass()->getArguments(),
                            ActionConfigBuilder::EAV_ENTITY_TYPE_CODE
                        ),
                        'optionSource' => $this->findArgumentByName(
                            $action->getConfigClass()->getArguments(),
                            ActionConfigBuilder::OPTION_SOURCE
                        ),
                        'identity' => $this->findArgumentByName(
                            $action->getConfigClass()->getArguments(),
                            ActionConfigBuilder::IDENTITY
                        )
                    ];
                }
            }
        }

        return $entityFieldModifiers;
    }

    private function getModifierObject(string $modifierClass, array $arguments = [])
    {
        $class = $this->configClassFactory->create([
            'baseType'  => FieldModifierInterface::class,
            'name'      => $modifierClass,
            'arguments' => $arguments
        ]);

        return $this->factory->createObject($class);
    }

    private function isSystemAction(array $arguments): bool
    {
        return (bool)$this->findArgumentByName($arguments, ActionConfigBuilder::SYSTEM_ACTION);
    }

    private function isPreselected(array $arguments): bool
    {
        return (bool)$this->findArgumentByName($arguments, ActionConfigBuilder::IS_PRESELECTED);
    }

    private function findArgumentByName(array $arguments, string $name)
    {
        foreach ($arguments as $argument) {
            if ($argument->getName() == $name) {
                return $argument->getValue();
            }
        }

        return '';
    }

    public function getGroupLabel(string $group): string
    {
        $groupLabels = [
            self::TEXT_GROUP => __('Text Modifiers')->getText(),
            self::NUMERIC_GROUP => __('Numeric Modifiers')->getText(),
            self::DATE_GROUP => __('Date Modifiers')->getText(),
            self::CUSTOM_GROUP => __('Custom Modifiers')->getText()
        ];

        return $groupLabels[$group] ?? __('Custom Modifiers')->getText();
    }
}
