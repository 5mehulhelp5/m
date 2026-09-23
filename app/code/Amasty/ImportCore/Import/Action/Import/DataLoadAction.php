<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\Import;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Exception\JobDelegatedException;
use Amasty\ImportCore\Model\Batch\BatchRepository;

class DataLoadAction implements ActionInterface
{
    /**
     * @var BatchRepository
     */
    private $batchRepository;

    public function __construct(
        BatchRepository $batchRepository
    ) {
        $this->batchRepository = $batchRepository;
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        if ($importProcess->getBatchQty() === 0) {
            //batch qty read in prepare actions will be reset in core import
            $importProcess->setBatchQty(
                $this->batchRepository->countProcessBatches($importProcess->getIdentity())
            );
        }
        $batch = $this->batchRepository->fetchBatch($importProcess->getIdentity());

        if ($batch->getId()) {
            $importProcess->setData($batch->getBatchData());
            if ($importProcess->canFork()) {
                if ($importProcess->fork() > 0) { // parent
                    throw new JobDelegatedException(); // Break execution cycle and pass to the next batch
                }
            }
        } else {
            $importProcess->getImportResult()->terminateImport();
        }
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function initialize(ImportProcessInterface $importProcess): void
    {
    }
}
