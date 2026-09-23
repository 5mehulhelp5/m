<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\QuickPayForOrder\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Url\DecoderInterface;

class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var FileFactory
     */
    protected $downloader;
    
    /**
     * @var DirectoryList
     */
    protected $directory;
    
    /**
     * @var QuickPayOrdersFactory
     */
    protected $quickPayOrdersFactory;
    
    /**
     * @var File
     */
    protected $fileSystemIo;
    
    protected $_downloader;
    
    protected $_directory;
    
    protected $_quickPayOrdersFactory;
    
    protected $_fileSystemIo;
    
    protected $_fileDriver;
    
    protected $_urlDecoder;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param DirectoryList $directory
     * @param QuickPayOrdersFactory $quickPayOrdersFactory
     * @param File $fileSystemIo
     * @param FileDriver $fileDriver
     * @param DecoderInterface $urlDecoder
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        DirectoryList $directory,
        QuickPayOrdersFactory $quickPayOrdersFactory,
        File $fileSystemIo,
        FileDriver $fileDriver,
        DecoderInterface $urlDecoder
    ) {
        $this->_downloader =  $fileFactory;
        $this->_directory = $directory;
        $this->_quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->_fileSystemIo = $fileSystemIo;
        $this->_fileDriver = $fileDriver;
        $this->_urlDecoder = $urlDecoder;
        parent::__construct($context);
    }

    /**
     * return file content
     *
     * @return null|\Magento\Framework\App\Response\Http\File
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $payid = $this->getRequest()->getParam('id');
        if ($payid == '') {
            $this->messageManager->addError(__("Invalid Params"));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $payid = $this->_urlDecoder->decode($payid);
        $payOrder = $this->_quickPayOrdersFactory->create()->load($payid);
        $fileName = $payOrder->getAttachment();
        if ($fileName) {
            try {
                $file = $this->_directory->getPath(
                    \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
                ). DIRECTORY_SEPARATOR . $fileName;
                $name = $this->_fileSystemIo->getPathInfo($fileName, PATHINFO_BASENAME);
                if (!$this->_fileDriver->isExists($file)) {
                    $this->messageManager->addError(__("File not exist"));
                    return $resultRedirect->setPath($this->_redirect->getRefererUrl());
                }
                return $this->_downloader->create(
                    $name['basename'],
                    [
                        'type' => "filename",
                        'value' => $fileName,
                        'rm' => false
                    ],
                    \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath($this->_redirect->getRefererUrl());
            }
        } else {
            $this->messageManager->addError(__("Quote doesn't have any attachment."));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }
    }
}
