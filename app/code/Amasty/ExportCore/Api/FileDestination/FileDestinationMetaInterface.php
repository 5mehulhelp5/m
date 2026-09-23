<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\FileDestination;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Magento\Framework\App\RequestInterface;

interface FileDestinationMetaInterface
{
    public function getMeta(): array;

    public function prepareConfig(
        ProfileConfigInterface $profileConfig,
        RequestInterface $request
    ): FileDestinationMetaInterface;
}
