<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Form\Fields;

use Amasty\ImportCore\Api\Config\Entity\Field\Configuration\PreselectedInterface;
use Amasty\ImportCore\Api\Config\Entity\Field\FieldInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Config\RelationConfigProvider;

class RequiredFieldsProvider
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var RelationConfigProvider
     */
    private $relationConfigProvider;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        RelationConfigProvider $relationConfigProvider
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->relationConfigProvider = $relationConfigProvider;
    }

    public function get(string $entityCode, ?string $behaviorCode, ?string $identifier = null): array
    {
        if (!$behaviorCode) {
            return [];
        }
        $result = [];
        $result[$entityCode] = $this->getFields($entityCode, $behaviorCode, $identifier);
        $relationConfig = $this->relationConfigProvider->get($entityCode);

        foreach ($relationConfig as $config) {
            $preselected = $config->getPreselected();
            if (!$preselected || !$this->checkBehavior($preselected, $behaviorCode)) {
                continue;
            }

            $childEntityCode = $config->getChildEntityCode();
            $result[$entityCode][$childEntityCode] =
                $this->get($childEntityCode, $behaviorCode)[$childEntityCode];
        }

        return $result;
    }

    private function getFields(string $entityCode, string $behaviorCode, ?string $identifier = null): array
    {
        $entityConfig = $this->entityConfigProvider->get($entityCode);
        $result = $fields = [];

        foreach ($entityConfig->getFieldsConfig()->getFields() as $field) {
            if ($field->getPreselected()) {
                $fieldData = $this->prepareField($field, $behaviorCode);
                if ($fieldData) {
                    $fields[$field->getName()] = $fieldData;
                }
            }

            if ($identifier !== null
                && $field->getIdentification()
                && ($field->getName() == $identifier)
                && !isset($fields[$field->getName()])
            ) {
                $fields[$field->getName()] = ['code' => $field->getName()];
            }
        }

        $result['enabled'] = "1";
        $result['fields'] = array_values($fields);

        return $result;
    }

    private function prepareField(FieldInterface $field, ?string $behaviorCode): array
    {
        $preselected = $field->getPreselected();
        $fieldData = [];

        if ($this->checkBehavior($preselected, $behaviorCode)) {
            $fieldData['code'] = $field->getName();
        }

        return $fieldData;
    }

    private function checkBehavior(PreselectedInterface $preselected, string $behaviorCode): bool
    {
        if (($preselected->getIncludeBehaviors())
                && in_array($behaviorCode, $preselected->getIncludeBehaviors())
            || ($preselected->getExcludeBehaviors())
                && !in_array($behaviorCode, $preselected->getExcludeBehaviors())
        ) {
            return true;
        }

        return false;
    }
}
