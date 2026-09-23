<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Test\Integration\Utils;

use Amasty\GiftCard\Model\Image\ImageBakingProcessor;
use Amasty\GiftCard\Test\Integration\Traits\ImageUpload;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

class FileUploadTest extends \PHPUnit\Framework\TestCase
{
    use ImageUpload;

    public const TEST_CODE = 'test';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->fileUpload = $this->objectManager->create(FileUpload::class);
        $this->filesystem = $this->objectManager->create(Filesystem::class);
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testSaveFileToTmpDir(): string
    {
        $fileArray = $this->prepareCustomImage('test_tmp_file');

        $result = $this->fileUpload->saveFileToTmpDir($fileArray, 'test_tmp_file');
        $this->assertFileExists(
            $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath()
            . FileUpload::AMGIFTCARD_IMAGE_MEDIA_TMP_PATH . DIRECTORY_SEPARATOR . $result['name']
        );
        $this->assertFileDoesNotExist(
            $this->objectManager->create(Filesystem::class)->getDirectoryWrite(DirectoryList::TMP)
                ->getAbsolutePath('test')
        );

        return $result['file'];
    }

    /**
     * @depends testSaveFileToTmpDir
     *
     * @param string $tmpFilePath
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testMoveFileFromTemp(string $tmpFilePath)
    {
        $this->fileUpload->saveFromTemp($tmpFilePath, false);
        $absoluteMediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $this->assertFileExists(
            $absoluteMediaPath
            . FileUpload::AMGIFTCARD_IMAGE_MEDIA_PATH
            . DIRECTORY_SEPARATOR . FileUpload::ADMIN_UPLOAD_PATH
            . DIRECTORY_SEPARATOR . $tmpFilePath
        );
        $this->assertFileDoesNotExist(
            $absoluteMediaPath . FileUpload::AMGIFTCARD_IMAGE_MEDIA_TMP_PATH
            . DIRECTORY_SEPARATOR . $tmpFilePath
        );
    }

    public static function tearDownAfterClass(): void
    {
        $writer = Bootstrap::getObjectManager()->create(Filesystem::class)
            ->getDirectoryWrite(DirectoryList::MEDIA);

        if ($writer->isExist('amasty/amgcard')) {
            $writer->delete('amasty/amgcard');
        }
    }
}
