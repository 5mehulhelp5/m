<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Utils;

class CleanUpByProcessIdentity
{
    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    public function __construct(
        TmpFileManagement $tmpFileManagement
    ) {
        $this->tmpFileManagement = $tmpFileManagement;
    }

    public function execute(string $processIdentity)
    {
        $this->tmpFileManagement->cleanFiles($processIdentity);
    }
}
