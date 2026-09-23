<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Utils\Type\Table\Row\Header;

class DataHandler
{
    public function getHeaderForOutput(array $row, string $prefix = '', string $postfix = ''): array
    {
        $header = [];
        foreach ($row as $key => $value) {
            if (is_array($value)) {
                //phpcs:ignore
                $header = array_merge($header, $this->getHeaderForOutput($value, $key, $postfix));
            } else {
                $header[] = (!empty($prefix) ? $prefix . $postfix : '') . $key;
            }
        }

        return $header;
    }

    public function getHeaderStructureByMap(array $map): array
    {
        $result = [];
        foreach ($map['fields'] as $field => $alias) {
            $result[!empty($alias) ? $alias : $field] = '';
        }
        if (!empty($map['subentities'])) {
            foreach ($map['subentities'] as $field => $subentity) {
                $result[!empty($subentity['map']) ? $subentity['map'] : $field] = $this->getHeaderStructureByMap(
                    $subentity
                );
            }
        }

        return $result;
    }
}
