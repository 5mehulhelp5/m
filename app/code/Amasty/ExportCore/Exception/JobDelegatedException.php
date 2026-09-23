<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Exception;

/**
 * Occurs when parent process delegates all remaining group actions to child process
 */
class JobDelegatedException extends \RuntimeException
{
}
