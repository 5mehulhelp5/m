<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Exception;

/**
 * Occurs when parent process delegates all remaining group actions to child process
 */
class JobDelegatedException extends \RuntimeException
{
}
