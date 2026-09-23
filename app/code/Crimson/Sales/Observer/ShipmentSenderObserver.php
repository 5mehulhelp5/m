<?php

namespace Crimson\Sales\Observer;

use Magento\Framework\Event\ObserverInterface;

class ShipmentSenderObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();

        /** @var \Magento\Framework\DataObject $transportObject */
        $transportObject = $observer->getEvent()->getData('transportObject');

        /** @var \Magento\Sales\Model\Order $order */
        $order = null; //$transport->getOrder();

        if (isset($transport['order'])) {
            $order = $transport['order'];
        }

        if($order && $order->getId()) {
            $transport['created_at_formatted'] = $order->getCreatedAtFormatted(2);
            $observer->getEvent()->setData('transport', $transport);
            $transportObject->setData($transport);
        }
    }
}
