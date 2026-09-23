<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Parallelization;

use Amasty\ExportCore\Api\ExportResultInterface;

class ResultMerger
{
    public function merge(ExportResultInterface $primaryResult, ExportResultInterface $secondaryResult)
    {
        $primaryResult->setRecordsProcessed(
            $primaryResult->getRecordsProcessed() + $secondaryResult->getRecordsProcessed()
        );

        foreach ($secondaryResult->getMessages() as $message) {
            $primaryResult->logMessage($message['type'], $message['message']);
        }

        if ($secondaryResult->isExportTerminated()) {
            $primaryResult->terminateExport($secondaryResult->isFailed());
        }
    }
}
