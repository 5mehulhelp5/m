<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Controller\Adminhtml\Export;

use Amasty\ExportCore\Api\ExportResultInterface;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Amasty\ExportCore\Model\Process\Process;
use Amasty\ExportCore\Processing\JobManager;
use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;

class Download extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'Amasty_ExportCore::export';

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Action\Context $context,
        TmpFileManagement $tmp,
        FileFactory $fileFactory,
        JobManager $jobManager
    ) {
        parent::__construct($context);
        $this->jobManager = $jobManager;
        $this->tmp = $tmp;
        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {
        if ($processIdentity = $this->getRequest()->getParam('processIdentity')) {
            /** @var $exportResult ExportResultInterface */
            /** @var $process Process */
            [$process, $exportResult] = $this->jobManager->watchJob($processIdentity)->getJobState();
            if ($exportResult !== null
                && $process->getStatus() === Process::STATUS_SUCCESS
                && $exportResult->getResultFileName()
            ) {
                $tmpFilename = $this->tmp->getResultTempFileName($process->getIdentity());
                $tempDirectory = $this->tmp->getTempDirectory($process->getIdentity());
                $absolutePath = $tempDirectory->getAbsolutePath($tmpFilename);
                if (!$this->tmp->getTempDirectory()->isExist($absolutePath)
                    || !$tempDirectory->stat($tmpFilename)['size']
                ) {
                    $this->messageManager->addErrorMessage(__('Export File is empty'));

                    return $this->resultRedirectFactory->create()->setRefererUrl();
                }

                return $this->fileFactory->create(
                    $exportResult->getResultFileName(),
                    [
                        'type' => 'filename',
                        'value' => $absolutePath
                    ],
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/octet-stream',
                    $tempDirectory->stat($tmpFilename)['size']
                );
            }
        }

        $this->messageManager->addErrorMessage(__('Something went wrong'));

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
