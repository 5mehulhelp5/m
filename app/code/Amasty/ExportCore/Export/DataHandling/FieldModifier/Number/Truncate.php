<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\Number;

use Amasty\ExportCore\Export\DataHandling\ModifierProvider;

class Truncate extends AbstractNumberModifier
{
    public function transform($value)
    {
        $this->validateInput($value);

        return floor((float)$value);
    }

    public function getGroup(): string
    {
        return ModifierProvider::NUMERIC_GROUP;
    }

    public function getLabel(): string
    {
        return __('Truncate')->getText();
    }
}
