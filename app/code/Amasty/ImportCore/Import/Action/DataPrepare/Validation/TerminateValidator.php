<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\DataPrepare\Validation;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\OptionSource\ValidationStrategy;

class TerminateValidator
{
    public function isNeedToTerminate(ImportProcessInterface $importProcess): bool
    {
        $importProcess->increaseErrorQuantity();
        if ($importProcess->getProfileConfig()->getValidationStrategy() == ValidationStrategy::STOP_ON_ERROR) {
            return true;
        } elseif ($importProcess->getErrorQuantity() >= $importProcess->getProfileConfig()->getAllowErrorsCount()) {
            return true;
        }

        return false;
    }
}
