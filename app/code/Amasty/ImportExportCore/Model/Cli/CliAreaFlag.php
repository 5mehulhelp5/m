<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\Cli;

class CliAreaFlag
{
    /**
     * @var bool
     */
    private $flag = false;

    public function isCliArea(): bool
    {
        return $this->flag;
    }

    public function setIsCliArea(bool $flag): void
    {
        $this->flag = $flag;
    }
}
