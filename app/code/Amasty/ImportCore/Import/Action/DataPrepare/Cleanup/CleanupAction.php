<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\DataPrepare\Cleanup;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\Action\CleanerInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;

class CleanupAction implements ActionInterface
{
    /**
     * @var CleanerProvider
     */
    private $cleanerProvider;

    /**
     * @var CleanerInterface[]
     */
    private $cleaners;

    public function __construct(CleanerProvider $cleanerProvider)
    {
        $this->cleanerProvider = $cleanerProvider;
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        if ($importProcess->getBatchNumber() != 1) {
            return;
        }

        foreach ($this->cleaners as $cleaner) {
            $cleaner->clean($importProcess);
        }
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        $this->cleaners = $this->cleanerProvider->getCleaners(
            $importProcess->getProfileConfig()->getEntityCode()
        );
    }
}
