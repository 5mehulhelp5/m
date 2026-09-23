<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Utils;

class OptionsProcessor
{
    public function process(array $options, bool $allowValueIsArray = true): array
    {
        $preparedOptions = [];

        foreach ($options as $option) {
            if (!is_array($option)) {
                continue;
            }

            if ($this->isValid($option, $allowValueIsArray)) {
                $option['value'] = !is_array($option['value'])
                    ? (string)$option['value']
                    : $option['value'];

                $preparedOptions[] = $option;
            } else {
                array_push($preparedOptions, ...$this->process($option));
            }
        }

        return $preparedOptions;
    }

    private function isValid(array $option, bool $allowValueIsArray): bool
    {
        return isset($option['value'])
            && ($allowValueIsArray || !is_array($option['value']));
    }
}
