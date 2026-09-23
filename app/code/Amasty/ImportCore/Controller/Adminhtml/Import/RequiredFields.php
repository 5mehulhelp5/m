<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Controller\Adminhtml\Import;

use Amasty\ImportCore\Import\Form\Fields\RequiredFieldsProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;

class RequiredFields extends Action
{
    /**
     * @var RequiredFieldsProvider
     */
    private $requiredFieldsProvider;

    public function __construct(
        Context $context,
        RequiredFieldsProvider $requiredFieldsProvider
    ) {
        parent::__construct($context);
        $this->requiredFieldsProvider = $requiredFieldsProvider;
    }

    public function execute()
    {
        $result = [];
        $request = $this->getRequest();

        $entityCode = $request->getParam('entity_code');
        if ($entityCode) {
            $result = $this->requiredFieldsProvider->get(
                $entityCode,
                $request->getParam('behavior_code'),
                $request->getParam('identifier')
            );
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
