<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Controller\Adminhtml\Image;

use Amasty\GiftCard\Controller\Adminhtml\AbstractImage;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class UploadTemp extends AbstractImage
{
    /**
     * @var FileUpload
     */
    private $fileUpload;

    public function __construct(
        Action\Context $context,
        FileUpload $fileUpload
    ) {
        parent::__construct($context);
        $this->fileUpload = $fileUpload;
    }

    public function execute()
    {
        try {
            $file = $this->getRequest()->getFiles('image');
            $result = $this->fileUpload->saveFileToTmpDir($file);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
