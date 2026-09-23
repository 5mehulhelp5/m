<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Processing;

use Amasty\ImportCore\Api\ImportResultInterface;
use Amasty\ImportCore\Api\ImportResultInterfaceFactory;
use Amasty\ImportCore\Model\Process\Process;
use Amasty\ImportCore\Model\Process\ProcessRepository;

class JobWatcher
{
    /**
     * @var int
     */
    protected $processIdentity;

    /**
     * @var ProcessRepository
     */
    private $processRepository;

    /**
     * @var ImportResultInterfaceFactory
     */
    private $importResultFactory;

    public function __construct(
        ProcessRepository $processRepository,
        ImportResultInterfaceFactory $importResultFactory,
        ?string $processIdentity = null
    ) {
        $this->processIdentity = $processIdentity;
        $this->processRepository = $processRepository;
        $this->importResultFactory = $importResultFactory;
    }

    /**
     * @return array
     */
    public function getJobState()
    {
        $process = $this->getProcess();
        $importResultData = $process->getImportResult();
        if ($importResultData) {
            /** @var ImportResultInterface $importResult */
            $importResult = $this->importResultFactory->create();
            $importResult->unserialize($importResultData);
        } else {
            $importResult = null;
        }

        return [$process, $importResult];
    }

    protected function getProcess(): Process
    {
        return $this->processRepository->getByIdentity($this->processIdentity);
    }
}
