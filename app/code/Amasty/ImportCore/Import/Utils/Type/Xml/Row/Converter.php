<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Utils\Type\Xml\Row;

class Converter
{
    public const TAB_SIZE = 2;

    public function convert($profileConfig, array $data, int $level = 0): string
    {
        $xmlSourceConfig = $profileConfig->getExtensionAttributes()->getXmlSource();
        if (!empty($profileConfig->getEntitiesConfig()->getMap())) {
            $itemTag = $profileConfig->getEntitiesConfig()->getMap();
        } else {
            $itemPath = $xmlSourceConfig->getItemPath();
            $itemPathParts = explode('/', $itemPath);
            $itemTag = end($itemPathParts);
        }

        $contentIndent = str_repeat(' ', ($level * 2 + 1) * self::TAB_SIZE);
        $rowIndent = str_repeat(' ', ($level * 2 + 2) * self::TAB_SIZE);

        $result = [];
        foreach ($data as $row) {
            $rowData = '';
            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    $nodeValue = $this->convert(
                        $profileConfig,
                        $value,
                        $level + 1
                    );
                    $rowData .=  "{$rowIndent}<{$key}>\n{$nodeValue}{$rowIndent}</{$key}>\n";
                } else {
                    //phpcs:ignore
                    $value = is_string($value) ? htmlspecialchars($value, ENT_XML1) : $value;
                    $rowData .= "{$rowIndent}<{$key}>{$value}</{$key}>\n";
                }
            }

            $result []= "{$contentIndent}<{$itemTag}>\n{$rowData}{$contentIndent}</{$itemTag}>\n";
        }

        return implode($result);
    }
}
