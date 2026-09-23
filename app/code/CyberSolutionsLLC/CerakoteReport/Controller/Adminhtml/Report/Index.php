<?php
/**
 * Copyright © Cyber Solutions LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CyberSolutionsLLC\CerakoteReport\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Admin page for the Cerakote Production Report grid.
 */
class Index extends Action
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'CyberSolutionsLLC_CerakoteReport::cerakote_production_report';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Render the Cerakote Production Report page.
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('CyberSolutionsLLC_CerakoteReport::cerakote_production_report');
        $resultPage->getConfig()->getTitle()->prepend(__('Cerakote Production Report'));
        return $resultPage;
    }
}
