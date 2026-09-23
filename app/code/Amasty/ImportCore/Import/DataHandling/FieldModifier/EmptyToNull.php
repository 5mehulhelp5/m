<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;

class EmptyToNull extends AbstractModifier implements FieldModifierInterface
{
    public function transform($value)
    {
        return trim((string)$value) === ''
            ? null
            : $value;
    }

    public function getGroup(): string
    {
        return ModifierProvider::TEXT_GROUP;
    }

    public function getLabel(): string
    {
        return __('Empty to Null')->getText();
    }
}
