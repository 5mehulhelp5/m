<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Process;

interface StatusCheckerInterface
{
    public function check(string $processIdentity): ProcessStatusInterface;
}
