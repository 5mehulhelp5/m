<?php

namespace CyberSolutionsLLC\ShipmentEmail\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Psr\Log\LoggerInterface;

class SendShipmentEmailAfterTracking implements ObserverInterface
{
    protected $shipmentSender;
    protected $logger;

    public function __construct(
        ShipmentSender $shipmentSender,
        LoggerInterface $logger
    ) {
        $this->shipmentSender = $shipmentSender;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {

        $shipment = $observer->getEvent()->getShipment();

        if (!$shipment) {
            return;
        }

        // Only send email if there is at least one tracking number
        $tracks = $shipment->getTracksCollection();
        $trackCount = $tracks->getSize();

        if ($trackCount > 0 && !$shipment->getEmailSent()) {
            try {
                $this->shipmentSender->send($shipment);
                $shipment->setEmailSent(true);
                $shipment->save(); // Save email_sent flag
            } catch (\Exception $e) {
                $this->logger->error(
                    'Shipment email send failed: ' . $e->getMessage()
                );
            }
        }
    }
}