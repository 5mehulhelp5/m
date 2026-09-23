<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Csv;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\Source\SourceReaderInterface;
use Amasty\ImportCore\Import\FileResolver\FileResolverAdapter;
use Amasty\ImportCore\Import\Source\Data\DataStructureProvider;
use Amasty\ImportCore\Import\Source\Utils\FileRowToArrayConverter;
use Amasty\ImportCore\Import\Source\Utils\HeaderStructureProcessor;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadInterface as FileReader;

class Reader implements SourceReaderInterface
{
    public const TYPE_ID = 'csv';

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var FileResolverAdapter
     */
    private $fileResolverAdapter;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DataStructureProvider
     */
    private $dataStructureProvider;

    /**
     * @var FileRowToArrayConverter
     */
    private $fileRowToArrayConverter;

    /**
     * @var HeaderStructureProcessor
     */
    private $headerStructureProcessor;

    /**
     * @var array
     */
    protected $headerStructure;

    /**
     * Storage for next row if 'merge row' setting is disabled
     * @var array
     */
    protected $nextRow;

    public function __construct(
        FileResolverAdapter $fileResolverAdapter,
        Filesystem $filesystem,
        DataStructureProvider $dataStructureProvider,
        FileRowToArrayConverter $fileRowToArrayConverter,
        HeaderStructureProcessor $headerStructureProcessor
    ) {
        $this->fileResolverAdapter = $fileResolverAdapter;
        $this->filesystem = $filesystem;
        $this->dataStructureProvider = $dataStructureProvider;
        $this->fileRowToArrayConverter = $fileRowToArrayConverter;
        $this->headerStructureProcessor = $headerStructureProcessor;
    }

    public function initialize(ImportProcessInterface $importProcess)
    {
        $fileName = $this->fileResolverAdapter->getFileResolver(
            $importProcess->getProfileConfig()->getFileResolverType()
        )->execute($importProcess);
        $this->config = $importProcess->getProfileConfig()->getExtensionAttributes()->getCsvSource();

        $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->fileReader = $directoryRead->openFile($fileName);

        $dataStructure = $this->dataStructureProvider->getDataStructure(
            $importProcess->getEntityConfig(),
            $importProcess->getProfileConfig()
        );
        $this->headerStructure = $this->headerStructureProcessor->getHeaderStructure(
            $dataStructure,
            $this->readCsvRow(),
            $this->config->getPrefix()
        );
    }

    public function readRow()
    {
        if (!$this->nextRow) {
            $rowData = $this->readCsvRow();
        } else {
            $rowData = $this->nextRow;
        }

        if (!is_array($rowData)) {
            return false;
        }
        $rowData = $this->fileRowToArrayConverter->convertRowToHeaderStructure(
            $this->headerStructure,
            $rowData
        );

        if ($this->config->isCombineChildRows()) {
            $rowData = $this->fileRowToArrayConverter->formatMergedSubEntities(
                $rowData,
                $this->headerStructure,
                $this->config->getChildRowSeparator()
            );
        } else {
            $rowData = $this->checkAndMergeSubEntities($rowData);
        }

        return $rowData;
    }

    public function estimateRecordsCount(): int
    {
        $rows = 0;
        $filePosition = $this->fileReader->tell();
        while (!$this->fileReader->eof()) {
            if (!$this->readAndValidateEntityRow()) {
                break;
            }

            $rows++;
        }

        $this->clearNextRowCache();
        $this->fileReader->seek($filePosition);

        return $rows;
    }

    protected function readCsvRow()
    {
        do {
            $rowData = $this->fileReader->readCsv(
                $this->config->getMaxLineLength(),
                $this->config->getSeparator(),
                $this->config->getEnclosure()
            );
            if (!is_array($rowData)) {
                return false;
            }

            foreach ($this->headerStructureProcessor->getColNumbersToSkip() as $key) {
                unset($rowData[$key]);
            }
        } while ($this->isRowEmpty($rowData));

        return array_values($rowData);
    }

    protected function checkAndMergeSubEntities(array $currentRow)
    {
        $this->nextRow = $this->readCsvRow();

        if (!$this->isNextRowValidForMergeProcessing()) {
            return $currentRow;
        }

        do {
            $nextRow = $this->fileRowToArrayConverter->convertRowToHeaderStructure(
                $this->headerStructure,
                $this->nextRow
            );

            $currentRow = $this->fileRowToArrayConverter->mergeRows(
                $currentRow,
                $nextRow,
                $this->headerStructure
            );
            $this->nextRow = $this->readCsvRow();
        } while ($this->isNextRowValidForMergeProcessing());

        return $currentRow;
    }

    private function readAndValidateEntityRow(): bool
    {
        if (!$this->nextRow) {
            $rowData = $this->readCsvRow();
        } else {
            $rowData = $this->nextRow;
        }

        if (!is_array($rowData)) {
            return false;
        }

        if (!$this->config->isCombineChildRows()) {
            $this->readSubEntitiesRow();
        }

        return true;
    }

    /**
     * Reads the next line of data.
     * Used to move the pointer in the file to the next row of entity data
     *
     * @return void
     */
    private function readSubEntitiesRow(): void
    {
        $this->nextRow = $this->readCsvRow();

        if (!$this->isNextRowValidForMergeProcessing()) {
            return;
        }

        do {
            $this->nextRow = $this->readCsvRow();
        } while ($this->isNextRowValidForMergeProcessing());
    }

    private function clearNextRowCache(): void
    {
        $this->nextRow = null;
    }

    private function isNextRowValidForMergeProcessing(): bool
    {
        if (!is_array($this->nextRow)) {
            return false;
        }

        $row = $this->fileRowToArrayConverter->convertRowToHeaderStructure(
            $this->headerStructure,
            $this->nextRow
        );

        return $this->isValidForMergeProcessing($row);
    }

    /**
     * Checks if row is potential main entity or extension of previous entity
     * @param array $row entity row for validation
     * @return bool
     */
    private function isValidForMergeProcessing(array $row): bool
    {
        if (empty($row)) {
            return false;
        }

        $firstItem = $row[array_key_first($row)];
        if (is_array($firstItem)) { // entity doesn't have fields, only subentities
            return false;
        }

        foreach ($row as $value) {
            if (!is_array($value) && !empty($value)) {
                return false;
            }
        }

        return true;
    }

    private function isRowEmpty(array $row): bool
    {
        return empty(array_filter($row));
    }
}
