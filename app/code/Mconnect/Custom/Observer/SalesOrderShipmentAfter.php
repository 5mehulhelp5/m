<?php

namespace Mconnect\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentAfter implements ObserverInterface
{


    protected function _isValidForShipmentEmail($shipment)
    {
        // \Magento\Framework\App\ObjectManager::getInstance()
        //     ->get(\Psr\Log\LoggerInterface::class)->info(__METHOD__);
        $trackingNumbers = array();
        foreach ($shipment->getAllTracks() as $track) {
            $trackingNumbers[] = $track->getNumber();
        };

        \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Psr\Log\LoggerInterface::class)->info("Tracking Numbers");
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Psr\Log\LoggerInterface::class)->info(print_r($trackingNumbers,true));

        // send shipment email only when carrier tracking info is added
        if (count($trackingNumbers) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // \Magento\Framework\App\ObjectManager::getInstance()
        //     ->get(\Psr\Log\LoggerInterface::class)->info(__METHOD__);
        /* @var $shipment Mage_Sales_Model_Order_Shipment */
        // $shipment = $observer->getEvent()->getShipment();

        // $event = $observer->getEvent();
        // $track = $event->getTrack();
        // $shipment = $track->getShipment();
        $shipment = $observer->getEvent()->getShipment();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        // $tracksCollection = $shipment->getTracksCollection();

        // foreach ($tracksCollection->getItems() as $track) {
        //     $track_number = $track->getTrackNumber();
        //     $carrier_name = $track->getTitle();
        // }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $ShipmentSender = $objectManager->get('\Magento\Sales\Model\Order\Email\Sender\ShipmentSender');
        // $shipmentRepo = $objectManager->get('\Magento\Sales\Api\ShipmentRepositoryInterface');

        // $shipmentId = $shipment->getId();
        
        // $temp = $shipmentRepo->get($shipmentId);
        \Magento\Framework\App\ObjectManager::getInstance()
        ->get(\Psr\Log\LoggerInterface::class)->info(__METHOD__);
        //     \Magento\Framework\App\ObjectManager::getInstance()
        //     ->get(\Psr\Log\LoggerInterface::class)->info("Tracks Repo");



        if ($shipment) {
            // \Magento\Framework\App\ObjectManager::getInstance()
            // ->get(\Psr\Log\LoggerInterface::class)->info("Outside of _isValidForShipmentEmail");
            if ($this->_isValidForShipmentEmail($shipment)) {
                \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Psr\Log\LoggerInterface::class)->info(__METHOD__);
            //     \Magento\Framework\App\ObjectManager::getInstance()
            // ->get(\Psr\Log\LoggerInterface::class)->info(print_r(get_class_methods($shipment),true));
                // $shipment->sendEmail();
                $shipment->setEmailSent(true);
                $ShipmentSender->send($shipment);
            }
        }

        return $this;

    }
}