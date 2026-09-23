<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader;

use Amasty\Base\Model\Response\OctetResponseInterface;

interface AccountImageDownloaderInterface
{
    public function execute(): OctetResponseInterface;
}
