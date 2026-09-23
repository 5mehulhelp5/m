<?php

namespace CyberSolutionsLLC\ShipmentEmail\Plugin;

use Magento\Sales\Model\Order\Shipment;

class DisableShipmentEmail
{
    public function aroundSend(
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $subject,
        callable $proceed,
        Shipment $shipment,
        $forceSyncMode = false
    ) {
        // If shipment has no tracking then stop email
        if (!$shipment->getTracks() || count($shipment->getTracks()) == 0) {
            return false;
        }

        return $proceed($shipment, $forceSyncMode);
    }
}