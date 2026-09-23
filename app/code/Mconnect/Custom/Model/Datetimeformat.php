<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Custom\Model;

use Magento\Sales\Api\Data\OrderInterface;

class Datetimeformat extends \Magento\Sales\Model\Order {

    /**
     * Return created_at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(OrderInterface::CREATED_AT);
    }

    public function getDateforemail() {
        return date("m/d/Y", strtotime($this->getCreatedAt()));
    }
}