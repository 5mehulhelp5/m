<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader;

use Amasty\Base\Model\Response\OctetResponseInterface;
use Amasty\GiftCard\Utils\FileUpload;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils\AccountsToDownloadableFileConverter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Archive implements AccountImageDownloaderInterface
{
    public const ARCHIVE_FILENAME = 'giftcard-images';

    /**
     * @var Utils\ResponseBuilder
     */
    private $responseBuilder;

    /**
     * @var Utils\DownloadableFileFactory
     */
    private $downloadableFileFactory;

    /**
     * @var Archive\ArchiveAdapterInterface
     */
    private $archiveAdapter;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDir;

    /**
     * @var AccountsToDownloadableFileConverter
     */
    private $accountsConverter;

    /**
     * @var GiftCardAccountInterface[]
     */
    private $accounts;

    /**
     * @param Utils\ResponseBuilder $responseBuilder
     * @param Utils\DownloadableFileFactory $downloadableFileFactory
     * @param Archive\ArchiveAdapterInterface $archiveAdapter
     * @param Filesystem $filesystem
     * @param AccountsToDownloadableFileConverter $accountsConverter
     * @param GiftCardAccountInterface[] $accounts
     */
    public function __construct(
        Utils\ResponseBuilder $responseBuilder,
        Utils\DownloadableFileFactory $downloadableFileFactory,
        Archive\ArchiveAdapterInterface $archiveAdapter,
        Filesystem $filesystem,
        AccountsToDownloadableFileConverter $accountsConverter,
        array $accounts
    ) {
        $this->responseBuilder = $responseBuilder;
        $this->downloadableFileFactory = $downloadableFileFactory;
        $this->archiveAdapter = $archiveAdapter;
        $this->mediaDir = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->accountsConverter = $accountsConverter;
        $this->accounts = $accounts;
    }

    public function execute(): OctetResponseInterface
    {
        $fileName = self::ARCHIVE_FILENAME . '.' . $this->archiveAdapter->getExtension();
        $filePath = FileUpload::AMGIFTCARD_IMAGE_MEDIA_PATH . DIRECTORY_SEPARATOR . $fileName;
        if ($this->mediaDir->isExist($filePath)) {
            $this->mediaDir->delete($filePath);
        }

        $zipAbsolutePath = $this->mediaDir->getAbsolutePath($filePath);
        $this->archiveAdapter->pack($zipAbsolutePath, $this->accountsConverter->execute($this->accounts));
        $downloadableFile = $this->downloadableFileFactory->create();
        $downloadableFile->setFileName($fileName);
        $downloadableFile->setPath($zipAbsolutePath);

        return $this->responseBuilder->build($downloadableFile);
    }
}
