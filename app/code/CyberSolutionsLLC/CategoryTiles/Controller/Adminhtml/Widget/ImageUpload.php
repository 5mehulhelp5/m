<?php

declare(strict_types=1);

namespace CyberSolutionsLLC\CategoryTiles\Controller\Adminhtml\Widget;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

class ImageUpload extends Action
{
    const ADMIN_RESOURCE = 'Magento_Widget::widget_instance';

    private const UPLOAD_DIR = 'wysiwyg/category_tiles';

    private Filesystem $filesystem;
    private UploaderFactory $uploaderFactory;
    private StoreManagerInterface $storeManager;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $fileId = key($_FILES);
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $uploadResult = $uploader->save($mediaDir->getAbsolutePath(self::UPLOAD_DIR));

            $relativePath = self::UPLOAD_DIR . '/' . $uploadResult['file'];
            $mediaUrl = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            $result->setData([
                'path' => $relativePath,
                'url' => $mediaUrl . $relativePath,
                'file' => $uploadResult['file'],
            ]);
        } catch (\Exception $e) {
            $result->setData(['error' => $e->getMessage()]);
        }

        return $result;
    }
}


