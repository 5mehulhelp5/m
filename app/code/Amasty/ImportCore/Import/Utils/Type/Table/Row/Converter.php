<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Utils\Type\Table\Row;

use Amasty\ImportExportCore\Utils\Type\Table;
use Amasty\ImportCore\Import\Config\Source\Type\TableConfigAdapter;

class Converter
{
    /**
     * @var Table\ConvertRowTo2DimensionalArray
     */
    private $convertRowTo2DimensionalArray;

    /**
     * @var Table\ConvertRowToMergedList
     */
    private $convertRowToMergedList;

    public function __construct(
        Table\ConvertRowTo2DimensionalArray $convertRowTo2DimensionalArray,
        Table\ConvertRowToMergedList $convertRowToMergedList
    ) {
        $this->convertRowTo2DimensionalArray = $convertRowTo2DimensionalArray;
        $this->convertRowToMergedList = $convertRowToMergedList;
    }

    public function convert(
        TableConfigAdapter $tableConfigAdapter,
        array $data,
        array $headerStructure
    ): array {
        $rows = [];
        foreach ($data as $row) {
            if ($tableConfigAdapter->getIsCombineChildRows()) {
                $childRowDelimiter = $tableConfigAdapter->getChildRowSeparator();
                $convertedRow = $this->convertRowToMergedList->convert(
                    $row,
                    $headerStructure,
                    $childRowDelimiter
                );

                $rows[] = reset($convertedRow);
            } else {
                $convertedRowMatrix = $this->convertRowTo2DimensionalArray->convert(
                    $row,
                    $headerStructure,
                    $tableConfigAdapter->getIsDuplicateParentData()
                );
                foreach ($convertedRowMatrix as $convertedRow) {
                    $rows[] = $convertedRow;
                }
            }
        }

        return $rows;
    }
}
