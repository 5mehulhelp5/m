<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Controller\Adminhtml\Import;

use Amasty\ImportCore\Import\Utils\CleanUpByProcessIdentity;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;

class Cancel extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_ImportCore::import';

    /**
     * @var CleanUpByProcessIdentity
     */
    private $cleanUpByProcessIdentity;

    public function __construct(
        Action\Context $context,
        CleanUpByProcessIdentity $cleanUpByProcessIdentity
    ) {
        parent::__construct($context);
        $this->cleanUpByProcessIdentity = $cleanUpByProcessIdentity;
    }

    public function execute()
    {
        $result = [];

        $processIdentity = $this->getRequest()->getParam('processIdentity');
        if ($processIdentity) {
            $this->cleanUpByProcessIdentity->execute($processIdentity);
            $result['type'] = 'success';
        } else {
            $result['error'] = __('Process Identity is not set.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
