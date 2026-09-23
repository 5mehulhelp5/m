<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Controller\Adminhtml\Export;

use Amasty\ExportCore\Model\Process\StatusChecker;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Status extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_ExportCore::export';

    /**
     * @var StatusChecker
     */
    private $statusChecker;

    public function __construct(
        Action\Context $context,
        StatusChecker $statusChecker
    ) {
        parent::__construct($context);
        $this->statusChecker = $statusChecker;
    }

    public function execute()
    {
        $result = [];
        if ($processIdentity = $this->getRequest()->getParam('processIdentity')) {
            $result = $this->statusChecker->check($processIdentity)->__toArray();
        } else {
            $result['error'] = __('Process Identity is not set.');
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
