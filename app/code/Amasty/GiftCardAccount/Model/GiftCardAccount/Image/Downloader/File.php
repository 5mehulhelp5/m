<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader;

use Amasty\Base\Model\Response\OctetResponseInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;

class File implements AccountImageDownloaderInterface
{
    /**
     * @var Utils\AccountImageProcessor
     */
    private $accountImageProcessor;

    /**
     * @var Utils\ResponseBuilder
     */
    private $responseBuilder;

    /**
     * @var GiftCardAccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var int
     */
    private $accountId;

    public function __construct(
        GiftCardAccountRepositoryInterface $accountRepository,
        Utils\AccountImageProcessor $accountImageProcessor,
        Utils\ResponseBuilder $responseBuilder,
        int $accountId
    ) {
        $this->accountImageProcessor = $accountImageProcessor;
        $this->responseBuilder = $responseBuilder;
        $this->accountRepository = $accountRepository;
        $this->accountId = $accountId;
    }

    /**
     * @throws NotFoundException
     */
    public function execute(): OctetResponseInterface
    {
        $account = $this->accountRepository->getById($this->accountId);
        $downloadableFile = $this->accountImageProcessor->prepare($account);
        if ($downloadableFile === null) {
            throw new NotFoundException(__(
                'Can\'t find image to download for account ID %1.',
                $account->getAccountId()
            ));
        }

        return $this->responseBuilder->build($downloadableFile);
    }
}
