<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Controller\Adminhtml\Import;

use Amasty\ImportCore\Model\Process\ProcessRepository;
use Amasty\ImportCore\Processing\JobManager;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\LocalizedException;

class Import extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_ImportCore::import';

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var ProcessRepository
     */
    private $processRepository;

    public function __construct(
        Action\Context $context,
        ProcessRepository $processRepository,
        JobManager $jobManager
    ) {
        parent::__construct($context);
        $this->jobManager = $jobManager;
        $this->processRepository = $processRepository;
    }

    public function execute()
    {
        $result = ['type' => 'error'];

        $processIdentity = $this->getRequest()->getParam('processIdentity');
        if ($processIdentity) {
            try {
                $profileConfig = $this->processRepository->getByIdentity($processIdentity)
                    ->getProfileConfig();
                $profileConfig->setStrategy('import');
                $this->jobManager->requestJob($profileConfig, $processIdentity);
                $result = ['type' => 'success'];
            } catch (LocalizedException $e) {
                $result['message'] = __('Requested Process Identity not found.');
            } catch (\Exception $e) {
                $result['message'] = $e->getMessage();
            }
        } else {
            $result['message'] = __('Process Identity is not set.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
