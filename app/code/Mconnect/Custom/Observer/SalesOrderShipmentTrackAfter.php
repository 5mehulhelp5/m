<?php

namespace Mconnect\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class SalesOrderShipmentTrackAfter implements ObserverInterface
{

    protected $shipmentSender;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        LoggerInterface $logger
    ){
        $this->shipmentSender = $shipmentSender;
        $this->logger = $logger;
    }

    protected function _isValidForShipmentEmail($shipment,$order)
    {
       $shipped = 0;
       $ordered = 0;
       foreach( $order->getAllVisibleItems() as $item ) {
           $shipped += $item->getQtyShipped();
           $ordered += $item->getQtyOrdered();
       }

        $trackingNumbers = array();
        foreach ($shipment->getAllTracks() as $track) {
            $trackingNumbers[] = $track->getNumber();
        };

        // send shipment email only when carrier tracking info is added

        if (count($trackingNumbers) > 0 && $shipped == $ordered ) {
            return true;
        } else {
            return false;
        }
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();
        $shipment = $track->getShipment();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();

        if ($shipment) {
            try {
                if ($this->_isValidForShipmentEmail($shipment, $order)) {
                    $shipment->setEmailSent(true);
                    $this->shipmentSender->send($shipment);
                }
            } catch (NoSuchEntityException $ex) {
                $this->logger->error('Shipment email error (NoSuchEntityException): ' . $ex->getMessage(), [
                    'exception' => $ex,
                    'shipment_id' => $shipment->getId(),
                    'order_id' => $order->getId()
                ]);
                return false;
            } catch (\Exception $ex) {
                $this->logger->error('Shipment email error: ' . $ex->getMessage(), [
                    'exception' => $ex,
                    'shipment_id' => $shipment->getId(),
                    'order_id' => $order->getId()
                ]);
                return false;
            }
        }

        return $this;
    }
}
