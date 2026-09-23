<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\DataPrepare\Source;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\Source\SourceReaderInterface;
use Amasty\ImportCore\Exception\JobDelegatedException;
use Amasty\ImportCore\Import\Source\SourceReaderAdapter;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;

class SourceAction implements ActionInterface
{
    public const DEFAULT_BATCH_SIZE = 500;

    /**
     * @var SourceReaderAdapter
     */
    private $sourceReaderAdapter;

    /**
     * @var SourceReaderInterface
     */
    private $sourceReader;

    /**
     * @var SourceDataProcessor
     */
    private $sourceDataProcessor;

    /**
     * @var Helper
     */
    private $resourceHelper;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SourceReaderAdapter $sourceReaderAdapter,
        SourceDataProcessor $sourceDataProcessor,
        Helper $resourceHelper,
        SerializerInterface $serializer
    ) {
        $this->sourceReaderAdapter = $sourceReaderAdapter;
        $this->sourceDataProcessor = $sourceDataProcessor;
        $this->resourceHelper = $resourceHelper;
        $this->serializer = $serializer;
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        $batchSize = $importProcess->getProfileConfig()->getBatchSize() ?: self::DEFAULT_BATCH_SIZE;
        if (!$importProcess->getImportResult()->getTotalRecords()) {
            $importProcess->getImportResult()->setTotalRecords(
                $this->sourceReader->estimateRecordsCount()
            );
            $totalRecords = $importProcess->getImportResult()->getTotalRecords();
            $batchQty = (int)ceil($totalRecords / $batchSize);
            $importProcess->setBatchQty($batchQty);
            $importProcess->getProfileConfig()->setBatchSize($batchSize);
        }

        $data = [];

        $maxBatchSize = $this->resourceHelper->getMaxDataSize();
        for ($i = 0; $i < $batchSize; $i++) {
            if ($maxBatchSize <= strlen($this->serializer->serialize($data))) {
                $importProcess->getProfileConfig()->setOverflowBatchSize($i);

                break;
            }

            $row = $this->sourceReader->readRow();
            if ($row) {
                $data[] = $this->sourceDataProcessor->convertToImportProcessStructure(
                    $importProcess,
                    $row
                );
            } else {
                break;
            }
        }

        if ($importProcess->getBatchQty() == $importProcess->getBatchNumber()) {
            $importProcess->setLastBatchReached(true);
        }

        if (empty($data)) {
            $importProcess->addErrorMessage((string)__('Empty data batch has been read. Please ensure that'
                . ' the file is not empty, contains root entity identifiers, and that the file structure aligns'
                . ' with the import profile configuration.'));
        }

        $importProcess->setData($data);

        if ($importProcess->canFork()) {
            if ($importProcess->fork() > 0) { // parent
                throw new JobDelegatedException(); // Break execution cycle and pass to the next batch
            }
        }

        if ($importProcess->getBatchNumber() == 1) {
            $importProcess->addInfoMessage((string)__('The data is being read.'));
        }
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        $this->sourceReader = $this->sourceReaderAdapter->getReader(
            $importProcess->getProfileConfig()->getSourceType()
        );
        $this->sourceReader->initialize($importProcess);
    }
}
