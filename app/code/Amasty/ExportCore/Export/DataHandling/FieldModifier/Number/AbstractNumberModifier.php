<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\Number;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractNumberModifier extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @param mixed $value
     *
     * @return void
     * @throws LocalizedException
     */
    protected function validateInput($value): void
    {
        if (!empty($value) && !is_numeric($value)) {
            throw new LocalizedException(__(
                'The numeric modifier \'%1\' cannot be applied to string values, please validate your data.',
                $this->getLabel()
            ));
        }
    }
}
