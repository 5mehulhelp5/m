<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Csv;

use Amasty\ImportCore\Api\Source\SourceGeneratorInterface;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\Config\Source\Type\TableConfigAdapter\Builder;
use Amasty\ImportCore\Import\Utils\Type\Table\Row\Converter;
use Amasty\ImportCore\Import\Utils\Type\Table\Row\Header\DataHandler;
use Magento\Framework\Filesystem\Driver\File as CsvFile;

class Generator implements SourceGeneratorInterface
{
    /**
     * @var CsvFile
     */
    private $file;

    /**
     * @var DataHandler
     */
    private $headerDataHandler;

    /**
     * @var Converter
     */
    private $rowConverter;

    /**
     * @var Builder
     */
    private $tableConfigAdapterBuilder;

    public function __construct(
        CsvFile $file,
        DataHandler $headerDataHandler,
        Converter $rowConverter,
        Builder $tableConfigAdapterBuilder
    ) {
        $this->file = $file;
        $this->headerDataHandler = $headerDataHandler;
        $this->rowConverter = $rowConverter;
        $this->tableConfigAdapterBuilder = $tableConfigAdapterBuilder;
    }

    public function generate(ProfileConfig $profileConfig, array $data): string
    {
        $resource = $this->file->fileOpen('php://memory', 'a+');

        $headerStructure = $this->getHeaderStructure($profileConfig, $data);
        $headerForOutput = $this->headerDataHandler->getHeaderForOutput(
            $headerStructure,
            $profileConfig->getEntitiesConfig()->getMap() ?? '',
            $profileConfig->getExtensionAttributes()->getCsvSource()->getPrefix()
        );
        $this->putCsv($profileConfig, $headerForOutput, $resource);

        $tableConfigAdapter = $this->tableConfigAdapterBuilder->build($profileConfig);
        $convertedRows = $this->rowConverter->convert($tableConfigAdapter, $data, $headerStructure);
        foreach ($convertedRows as $convertedRow) {
            $this->putCsv($profileConfig, $convertedRow, $resource);
        }

        $fileSize = $this->file->fileTell($resource);
        $this->file->fileSeek($resource, 0);
        $content = $this->file->fileRead($resource, $fileSize);
        $this->file->fileClose($resource);

        return $content;
    }

    public function getExtension(): string
    {
        return 'csv';
    }

    private function getHeaderStructure(ProfileConfig $profileConfig, array $data)
    {
        $headerMap = $this->headerDataHandler->getHeaderMap($profileConfig->getEntitiesConfig(), $data[0]);

        return $this->headerDataHandler->getHeaderStructureByMap($headerMap);
    }

    private function putCsv($profileConfig, $row, $resource)
    {
        $this->file->filePutCsv(
            $resource,
            $row,
            $profileConfig->getExtensionAttributes()->getCsvSource()->getSeparator(),
            $profileConfig->getExtensionAttributes()->getCsvSource()->getEnclosure()
        );
    }
}
