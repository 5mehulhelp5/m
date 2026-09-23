<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Template\Type\Csv;

use Amasty\ExportCore\Api\ChunkStorageInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\Template\RendererInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Magento\Framework\App\ObjectManager;

class Renderer implements RendererInterface
{
    public const TYPE_ID = 'csv';

    /**
     * @var DataToCsvFactory
     */
    private $dataToCsvFactory;

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    /**
     * @var int
     */
    private $colsCount;

    /**
     * @var array
     */
    private $headerStructure;

    /**
     * @var DataToCsv
     */
    private $dataToCsv;

    /**
     * @var Utils\ConvertRowTo2DimensionalArray
     */
    private $convertRowTo2DimensionalArray;

    /**
     * @var Utils\ConvertRowToMergedList
     */
    private $convertRowToMergedList;

    /**
     * @var ExportHeaderBuilder
     */
    private $exportHeaderBuilder;

    public function __construct(
        DataToCsvFactory $dataToCsvFactory,
        ?EntityConfigProvider $entityConfigProvider, // @deprecated
        ConfigInterfaceFactory $configFactory,
        TmpFileManagement $tmp,
        Utils\ConvertRowTo2DimensionalArray $convertRowTo2DimensionalArray,
        Utils\ConvertRowToMergedList $convertRowToMergedList,
        ?ExportHeaderBuilder $exportHeaderBuilder = null
    ) {
        $this->dataToCsvFactory = $dataToCsvFactory;
        $this->configFactory = $configFactory;
        $this->tmp = $tmp;
        $this->convertRowTo2DimensionalArray = $convertRowTo2DimensionalArray;
        $this->convertRowToMergedList = $convertRowToMergedList;
        $this->exportHeaderBuilder = $exportHeaderBuilder
            ?? ObjectManager::getInstance()->get(ExportHeaderBuilder::class);
    }

    public function render(
        ExportProcessInterface $exportProcess,
        ChunkStorageInterface $chunkStorage
    ) : RendererInterface {
        $this->initFormatter($exportProcess);

        $chunkIds = $chunkStorage->getAllChunkIds();
        ksort($chunkIds, SORT_NUMERIC);
        $resultTempFile = $this->tmp->getTempDirectory($exportProcess->getIdentity())->openFile(
            $this->tmp->getResultTempFileName($exportProcess->getIdentity())
        );

        $headerRendered = false;
        foreach ($chunkIds as $chunkId) {
            $data = $chunkStorage->readChunk($chunkId);
            if (empty($data)) {
                continue;
            }

            if (!$headerRendered) {
                $this->headerStructure = $exportProcess->getExtensionAttributes()->getHelper()->getHeaderStructure();
                $header = $this->exportHeaderBuilder->getHeader(
                    $this->headerStructure,
                    $exportProcess->getProfileConfig()->getFieldsConfig()->getMap() ?? '',
                    $exportProcess->getProfileConfig()->getExtensionAttributes()->getCsvTemplate()->getPostfix(),
                    $exportProcess
                );
                $this->colsCount = count($header);
                $resultTempFile->write($this->renderHeader($exportProcess, $header));
                $headerRendered = true;
            }

            foreach ($data as $row) {
                $resultTempFile->write(
                    $this->renderData($exportProcess, $this->convertRowData($exportProcess, $row))
                );
            }
            $chunkStorage->deleteChunk($chunkId);
        }

        $resultTempFile->close();

        return $this;
    }

    public function convertRowData(ExportProcessInterface $exportProcess, array $row): array
    {
        $templateConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getCsvTemplate();
        if ($templateConfig->isCombineChildRows()) {
            $childRowDelimiter = $templateConfig->getChildRowSeparator();

            return $this->convertRowToMergedList->convert($row, $this->headerStructure, $childRowDelimiter);
        }

        return $this->convertRowTo2DimensionalArray->convert(
            $row,
            $this->headerStructure,
            (bool)$templateConfig->isDuplicateParentData()
        );
    }

    /**
     * @deprecated Due to extension logic.
     * @see \Amasty\ExportCore\Export\Template\Type\Csv\ExportHeaderBuilder::getHeader
     */
    public function getHeader($row, $prefix = '', $postfix = ''): array
    {
        return $this->exportHeaderBuilder->getHeader($row, $prefix, $postfix);
    }

    public function getFileExtension(ExportProcessInterface $exportProcess): ?string
    {
        return 'csv';
    }

    protected function initFormatter(ExportProcessInterface $exportProcess)
    {
        $config = $exportProcess->getProfileConfig()->getExtensionAttributes()->getCsvTemplate() ?? null;

        if ($config) {
            $this->dataToCsv = $this->dataToCsvFactory->create([
                'delimiter' => $config->getSeparator(),
                'enclosure' => $config->getEnclosure()
            ]);
        } else {
            $this->dataToCsv = $this->dataToCsvFactory->create();
        }
    }

    protected function renderHeader(ExportProcessInterface $exportProcess, array $headerColumnNames): string
    {
        $config = $exportProcess->getProfileConfig()->getExtensionAttributes()->getCsvTemplate()
            ?? $this->configFactory->create();
        if ($config->isHasHeaderRow()) {
            $header = $this->dataToCsv->convert([$headerColumnNames]);
        } else {
            $header = '';
        }

        return $header;
    }

    protected function renderData(ExportProcessInterface $exportProcess, array $data): string
    {
        if (empty($data)) {
            return '';
        }

        return $this->dataToCsv->convert($data);
    }
}
