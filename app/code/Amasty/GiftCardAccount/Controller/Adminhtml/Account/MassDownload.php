<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Controller\Adminhtml\Account;

use Amasty\GiftCardAccount\Controller\Adminhtml\AbstractAccount;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Image\DownloaderFactory;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDownload extends AbstractAccount
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var DownloaderFactory
     */
    private $downloaderFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        DownloaderFactory $downloaderFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->downloaderFactory = $downloaderFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        try {
            return $this->downloaderFactory->create(
                DownloaderFactory::ARCHIVE_DOWNLOADER,
                ['accounts' => $collection->getItems()]
            )->execute();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('Can\'t download images right now. Please review the log and try again.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('amgcard/account/index');
    }
}
