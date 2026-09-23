<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

/**
 * @codeCoverageIgnore
 */
class Index extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_ImportCore::import';

    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ImportCore::import');
        $resultPage->addBreadcrumb(__('Amasty Import'), __('Amasty Import'));
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));

        return $resultPage;
    }
}
