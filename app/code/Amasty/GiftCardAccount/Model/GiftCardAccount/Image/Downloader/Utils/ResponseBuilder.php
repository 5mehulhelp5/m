<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils;

use Amasty\Base\Model\Response\OctetResponseInterface;
use Amasty\Base\Model\Response\OctetResponseInterfaceFactory;
use Magento\Framework\HTTP\Mime;

class ResponseBuilder
{
    /**
     * @var OctetResponseInterfaceFactory
     */
    private $octetResponseFactory;

    public function __construct(
        OctetResponseInterfaceFactory $octetResponseFactory
    ) {
        $this->octetResponseFactory = $octetResponseFactory;
    }

    public function build(DownloadableFile $file): OctetResponseInterface
    {
        $fileResponse = $this->octetResponseFactory->create($file->getPath());
        $fileResponse->setContentType(Mime::TYPE_OCTETSTREAM);
        $fileResponse->setFileName($file->getFileName());

        return $fileResponse;
    }
}
