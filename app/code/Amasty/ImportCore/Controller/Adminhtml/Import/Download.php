<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Controller\Adminhtml\Import;

use Amasty\ImportCore\Import\Config\ProfileConfigFactory;
use Amasty\ImportCore\Import\FormProvider;
use Amasty\ImportCore\Import\SampleData\FileContent;
use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Download extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_ImportCore::import';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var FileContent
     */
    private $fileContent;

    /**
     * @var ProfileConfigFactory
     */
    private $profileConfigFactory;

    /**
     * @var FormProvider
     */
    private $formProvider;

    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        FileContent $fileContent,
        ProfileConfigFactory $profileConfigFactory,
        FormProvider $formProvider
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->fileContent = $fileContent;
        $this->profileConfigFactory = $profileConfigFactory;
        $this->formProvider = $formProvider;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data = $this->getRequest()->getParam('encodedData');
        if (!empty($data)) {
            $params = $this->getRequest()->getParams();
            unset($params['encodedData']);
            $postData = \json_decode($data, true);
            $this->getRequest()->setParams(array_merge_recursive($params, $postData));
        }

        try {
            $entityCode = $this->getRequest()->getParam('import_behavior', [])['entity_code'] ?? null
                ?? $this->getRequest()->getParam('entity_code');
            $sourceType = $this->getRequest()->getParam('file_config', [])['source_type'] ?? null
                ?? $this->getRequest()->getParam('source_type');
            $formType = $this->getRequest()->getParam('import_behavior', [])['form_type'] ?? null
                ?? $this->getRequest()->getParam('form_type');
            if (!$entityCode || !$sourceType || !$formType) {
                throw new LocalizedException(__('Entity Code and Source Type and Form Type are required'));
            }

            /** @var \Amasty\ImportCore\Import\Config\ProfileConfig $profileConfig */
            $profileConfig = $this->profileConfigFactory->create();
            $profileConfig->setStrategy('validate_and_save');
            $profileConfig->setEntityCode($entityCode);
            $profileConfig->setSourceType($sourceType);
            $this->formProvider->get($formType)->prepareConfig($profileConfig, $this->getRequest());

            list($filename, $content) = $this->fileContent->get($profileConfig);

            $response = [
                'content' => 'data:application/octet-stream;base64,' . base64_encode($content),
                'filename' => $filename,
                'download' => true
            ];
        } catch (LocalizedException $e) {
            $response = [
                // for core
                'type' => 'error',
                'message' => $e->getMessage(),
                // for profile
                'error' => true,
                'messages' => [
                    'error' => $e->getMessage()
                ]
            ];
        }

        $resultPage->setData($response);

        return $resultPage;
    }
}
