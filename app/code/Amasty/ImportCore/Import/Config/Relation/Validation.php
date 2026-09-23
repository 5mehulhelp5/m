<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Relation;

use Amasty\ImportCore\Api\Config\Relation\RelationValidationInterface;
use Magento\Framework\DataObject;

class Validation extends DataObject implements RelationValidationInterface
{
    public const VALIDATION_CLASS = 'validation_class';
    public const EXCLUDE_BEHAVIORS = 'exclude_behaviors';
    public const INCLUDE_BEHAVIORS = 'include_behaviors';

    public function getConfigClass()
    {
        return $this->getData(self::VALIDATION_CLASS);
    }

    public function setConfigClass($configClass)
    {
        $this->setData(self::VALIDATION_CLASS, $configClass);
    }

    public function getExcludeBehaviors()
    {
        return $this->getData(self::EXCLUDE_BEHAVIORS) ?: [];
    }

    public function setExcludeBehaviors($behaviorCodes)
    {
        return $this->setData(self::EXCLUDE_BEHAVIORS, $behaviorCodes);
    }

    public function getIncludeBehaviors()
    {
        return $this->getData(self::INCLUDE_BEHAVIORS) ?: [];
    }

    public function setIncludeBehaviors($behaviorCodes)
    {
        return $this->setData(self::INCLUDE_BEHAVIORS, $behaviorCodes);
    }
}
