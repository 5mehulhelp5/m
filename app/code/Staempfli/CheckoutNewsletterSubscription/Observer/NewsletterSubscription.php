<?php

namespace Staempfli\CheckoutNewsletterSubscription\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\SubscriberFactory;

class NewsletterSubscription implements ObserverInterface
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    public function __construct(
        SubscriberFactory $subscriberFactory
    ) {
        $this->subscriberFactory = $subscriberFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        // $customerId = $quote->getCustomer()->getId();
            
        if ($quote->getCustomerEmail()
            && (bool)$quote->getData(\Staempfli\CheckoutNewsletterSubscription\Model\Data\NewsletterSubscription::NEWSLETTER_SUBSCRIPTION)
        ) {
            try {
                if($quote->getCustomer()->getId()){
                    $this->getSubscriberFactory()->subscribeCustomerById($quote->getCustomer()->getId());
                }else{
                    $this->getSubscriberFactory()->subscribe($quote->getCustomerEmail());
                }
                
            } catch (\Exception $e) {
                \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Psr\Log\LoggerInterface::class)->info($e->getMessage());
            }
        }
    }

    /**
     * @return \Magento\Newsletter\Model\Subscriber
     */
    private function getSubscriberFactory()
    {
        return $this->subscriberFactory->create();
    }
    
}
