<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\QuickPayForOrder\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class SalesOrderPlaceBefore implements ObserverInterface
{
    
    protected $quoteFactory;
    protected $quickPayOrdersFactory;
    protected $customerSession;
    protected $messageManager;
    
    /**
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory $quickPayOrdersFactory
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory $quickPayOrdersFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->customerSession = $session;
        $this->messageManager = $messageManager;
    }
 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payId = $this->customerSession->getQuickPayId();
        $quickPayOrder = $this->quickPayOrdersFactory->create()->load($payId);
        $quoteId = $this->customerSession->getQuickPayQuoteId();
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getRealOrderId();

        $orderComment = $this->customerSession->getQuickPayOrderComment();
        $quote = $this->quoteFactory->create()->load($quoteId);
        if ($quote->getIsActive()) {
            $quickPayOrder->setOrderId($orderId)
                ->setOrderStatus(1);
            $quickPayOrder->save();

            try {
                $quoteItems = [];
                foreach ($quote->getAllVisibleItems() as $quoteItem) {
                    $quoteItems[$quoteItem->getId()] = $quoteItem;
                }

                foreach ($order->getAllVisibleItems() as $orderItem) {
                    $quoteItemId = $orderItem->getQuoteItemId();
                    $quoteItem = $quoteItems[$quoteItemId];
                    $additionalOptions = $quoteItem->getOptionByCode('additional_options');
                    
                    if (!empty($additionalOptions)) {
                        $options = $orderItem->getProductOptions();
                        $options['additional_options'] = json_decode($additionalOptions->getValue());
                        $orderItem->setProductOptions($options);
                    }
                }
                $this->customerSession->unsQuickPayQuoteId();
            } catch (\Exception $e) {
                unset($quoteItems);
            }
            
            if ($orderComment) {
                $order->addStatusHistoryComment($orderComment);
            }
            $order->setSomeNonExistentProperty(true);
            $order->save();
        }
    }
}
