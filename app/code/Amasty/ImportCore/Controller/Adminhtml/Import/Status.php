<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Controller\Adminhtml\Import;

use Amasty\ImportCore\Model\Process\StatusChecker;
use Amasty\ImportCore\Processing\JobManager;
use Magento\Backend\App\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;

class Status extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_ImportCore::import';

    /**
     * @var StatusChecker
     */
    private $statusChecker;

    public function __construct(
        Action\Context $context,
        JobManager $jobManager, //@deprecated backward compatibility
        ?StatusChecker $statusChecker = null
    ) {
        parent::__construct($context);
        $this->statusChecker = $statusChecker ?: ObjectManager::getInstance()->get(StatusChecker::class);
    }

    public function execute()
    {
        $processIdentity = $this->getRequest()->getParam('processIdentity');
        if ($processIdentity) {
            $result = $this->statusChecker->check($processIdentity)->__toArray();
        } else {
            $result['error'] = __('Process Identity is not set.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
