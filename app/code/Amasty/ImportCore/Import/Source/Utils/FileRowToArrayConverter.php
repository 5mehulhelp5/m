<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Utils;

use Magento\Framework\Stdlib\ArrayManager;

/**
 * Used for conversion of row read from file to header structure format in CSV, ODS and XLSX type files
 */
class FileRowToArrayConverter
{
    public const ENTITY_ID_KEY = '1';
    public const PARENT_ID_KEY = '2';

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    public function convertRowToHeaderStructure(
        array $structure,
        array $rowData,
        int &$columnCounter = 0
    ): array {
        $convertedData = [];

        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                $convertedData[$key][] = $this->convertRowToHeaderStructure(
                    $value,
                    $rowData,
                    $columnCounter
                );
            } else {
                $convertedData[$key] = $rowData[$columnCounter] ?? null;
                $columnCounter++;
            }
        }

        return $convertedData;
    }

    public function formatMergedSubEntities(array $rowData, array $structure, string $rowSeparator): array
    {
        $mainEntityKey = $this->findIdKey(self::ENTITY_ID_KEY, $structure);

        $formattedData = [];
        foreach ($rowData as $key => $row) {
            if (is_array($row)) {
                $row = $this->processMergedSubEntity(
                    $row[0],
                    $structure[$key],
                    $rowSeparator,
                    isset($rowData[$mainEntityKey]) ? (string)$rowData[$mainEntityKey] : null
                );
            }
            $formattedData[$key] = $row;
        }

        return $formattedData;
    }

    public function mergeRows(array $firstRow, array $secondRow, array $structure): array
    {
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($firstRow));
        $iterator->attachIterator(new \ArrayIterator($secondRow));

        foreach ($iterator as $key => $row) {
            if (is_array($row[0]) && is_array($row[1])) {
                if (!$this->canMerge($row[0], $row[1], $this->getSubStructure($structure, $key[0]))) {
                    $firstRow[$key[0]][count($row[0])-1] = $this->mergeRows(
                        $row[0][count($row[0]) - 1] ?? $row[0],
                        $row[1][0] ?? $row[1],
                        $structure
                    );
                } else {
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction
                    $firstRow[$key[0]] = call_user_func_array('array_merge', [$row[0], $row[1]]);
                }
            } else {
                $firstRow[$key[0]] = $row[0];
            }
        }

        return $firstRow;
    }

    protected function processMergedSubEntity(
        array $subEntityArray,
        array $subEntityStructure,
        string $rowSeparator,
        ?string $parentId = null,
        int $innerCounter = 0
    ): array {
        $formattedData = $explodedSubEntities = $nestedSubEntities = [];
        foreach ($subEntityArray as $key => $row) {
            if (is_array($row)) {
                $nestedSubEntities[$key] = $row; // saving nested subentities for further processing
                continue;
            }
            $explodedSubEntities[$key] = explode($rowSeparator, (string)$row);
        }
        $explodedSubEntitiesCount = count($explodedSubEntities[array_key_first($explodedSubEntities)]);

        $uniqueSubEntities = [];
        if ($subEntityParentKey = $this->findIdKey(self::PARENT_ID_KEY, $subEntityStructure)) {
            $uniqueSubEntities = array_values(array_unique($explodedSubEntities[$subEntityParentKey]));
        }

        for ($i = 0; $i < $explodedSubEntitiesCount; $i++) {
            if ($subEntityParentKey
                && !$this->validateExplodedSubentity(
                    (string)$explodedSubEntities[$subEntityParentKey][$i],
                    $parentId,
                    $uniqueSubEntities,
                    $innerCounter
                )
            ) {
                continue;
            }

            foreach ($explodedSubEntities as $key => $row) {
                $formattedData[$i][$key] = $row[$i];
            }
        }
        $formattedData = array_values($formattedData);

        if (count($nestedSubEntities) > 0) {
            $subEntityMainKey = $this->findIdKey(self::ENTITY_ID_KEY, $subEntityStructure);
            foreach ($formattedData as &$row) {
                foreach ($nestedSubEntities as $subEntityKey => $subEntityData) {
                    $row[$subEntityKey] = $this->processMergedSubEntity(
                        $subEntityData[0],
                        $subEntityStructure[$subEntityKey],
                        $rowSeparator,
                        $subEntityMainKey ? (string)$row[$subEntityMainKey] : null,
                        $innerCounter
                    );
                }
                $innerCounter++;
            }
        }

        return $formattedData;
    }

    protected function findIdKey(string $keyType, array $structure)
    {
        foreach ($structure as $key => $value) {
            if (!is_array($value) && strpos($value, $keyType) !== false) {
                return $key;
            }
        }

        return false;
    }

    protected function canMerge(array $firstRow, array $secondRow, array $structure): bool
    {
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($firstRow));
        $iterator->attachIterator(new \ArrayIterator($secondRow));
        $idFieldName = $this->findIdKey(self::ENTITY_ID_KEY, $structure);

        foreach ($iterator as $row) {
            if (is_array($row[0]) && is_array($row[1])) {
                return $this->canMerge($row[0], $row[1], $structure);
            }
            if (isset($secondRow[$idFieldName])) {
                if (!empty($secondRow[$idFieldName])) {
                    return true;
                }

                return false;
            }

            foreach ($secondRow as $rowKey => $rowValue) {
                if (is_array($rowValue)) {
                    return false;
                }
                if (!empty($firstRow[$rowKey]) && !empty($rowValue)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    private function validateExplodedSubentity(
        string $subEntityParentKeyValue,
        ?string $parentId,
        array $uniqueSubEntities,
        int $innerCounter
    ): bool {
        $isValid = false;
        if ($parentId) { // trying to validate based on child-parent relation
            if ($subEntityParentKeyValue !== $parentId) {
                return false;
            }
            $isValid = true;
        }

        return !(!$isValid && !empty($uniqueSubEntities) // trying to validate based on parent keys order
            && $subEntityParentKeyValue !== $uniqueSubEntities[$innerCounter]);
    }

    private function getSubStructure(array $structure, string $key)
    {
        return $this->arrayManager->get($this->arrayManager->findPath($key, $structure), $structure);
    }
}
