<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Controller\Adminhtml\Account;

use Amasty\Base\Model\Response\OctetResponseInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Controller\Adminhtml\AbstractAccount;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Image\DownloaderFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Download extends AbstractAccount
{
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
        DownloaderFactory $downloaderFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->downloaderFactory = $downloaderFactory;
        $this->logger = $logger;
    }

    /**
     * @return OctetResponseInterface|Redirect
     */
    public function execute()
    {
        if (($accountId = (int)$this->getRequest()->getParam(GiftCardAccountInterface::ACCOUNT_ID, 0))) {
            try {
                return $this->downloaderFactory->create(
                    DownloaderFactory::FILE_DOWNLOADER,
                    ['accountId' => $accountId]
                )->execute();
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(
                    __('Can\'t download image right now. Please review the log and try again.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Gift Card Account ID must be specified to download image.'));
        }

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
