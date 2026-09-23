<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;

class AccountsToDownloadableFileConverter
{
    /**
     * @var AccountImageProcessor
     */
    private $accountImageProcessor;

    /**
     * @var GiftCardAccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(
        AccountImageProcessor $accountImageProcessor,
        GiftCardAccountRepositoryInterface $accountRepository
    ) {
        $this->accountImageProcessor = $accountImageProcessor;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param GiftCardAccountInterface[] $accounts
     *
     * @return DownloadableFile[]
     */
    public function execute(array $accounts): array
    {
        $files = [];
        foreach ($accounts as $account) {
            $files[] = $this->accountImageProcessor->prepare(
                $this->accountRepository->getById($account->getAccountId())
            );
        }

        return array_filter($files);
    }
}
