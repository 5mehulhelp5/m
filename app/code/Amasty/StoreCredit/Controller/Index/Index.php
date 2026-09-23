<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Controller\Index;

use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Session $customerSession,
        Registry $registry,
        Context $context,
        ConfigProvider $configProvider
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->configProvider = $configProvider;
    }

    /**
     * @return $this|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->configProvider->isEnabled()) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
        }

        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');
        }

        $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
