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
namespace Webkul\QuickPayForOrder\Model;

use Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class QuickPayOrders extends AbstractModel implements QuickPayOrderInterface, IdentityInterface
{
    const NOROUTE_ENTITY_ID = 'no-route';
    
    /**
    * Quickpayorder History cache tag
    */
    const CACHE_TAG = 'quickpayorder_history';

    /**
     * @var string
     */
    protected $cacheTag = 'quickpayorder_history';
 
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $eventPrefix = 'quickpayorder_history';
     
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\QuickPayForOrder\Model\ResourceModel\QuickPayOrders::class);
    }
    
    /**
     * Return Identifiers
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }
    
    /**
     * Retrieve entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITYID);
    }

    /**
     * Retrieve quote_id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTEID);
    }
    
    /**
     * Retrieve customer_id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMERID);
    }
    
    /**
     * Retrieve created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATEDAT);
    }
    
    /**
     * Retrieve order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDERID);
    }
     
    /**
     * Retrieve order_status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->getData(self::ORDERSTATUS);
    }

    /**
     * Retrieve attachment
     *
     * @return string
     */
    public function getAttachment()
    {
        return $this->getData(self::ATTACHMENT);
    }

    /**
     * Retrieve payment_methods
     *
     * @return string
     */
    public function getPaymentMethods()
    {
        return $this->getData(self::PAYMENTMETHODS);
    }
    
    /**
     * Retrieve shipping_method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getData(self::SHIPPINGMETHOD);
    }

    /**
     * Retrieve all_payment_methods
     *
     * @return string
     */
    public function getAllPaymentMethods()
    {
        return $this->getData(self::ALLPAYMENTMETHODS);
    }

    /**
     * Retrieve all_shipping_methods
     *
     * @return string
     */
    public function getAllShippingMethods()
    {
        return $this->getData(self::ALLSHIPPINGMETHODS);
    }
    
    /**
     * Set ENTITYID
     *
     * @param int $id
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITYID, $id);
    }
    
    /**
     * Set QUOTEID
     *
     * @param int $quoteId
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTEID, $quoteId);
    }
     
    /**
     * Set CUSTOMERID
     *
     * @param int $customerId
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMERID, $customerId);
    }
     
    /**
     * Set CREATEDAT
     *
     * @param string $createdAt
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATEDAT, $createdAt);
    }
     
    /**
     * Set ORDERID
     *
     * @param int $orderId
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDERID, $orderId);
    }
     
    /**
     * Set ORDERSTATUS
     *
     * @param string $orderStatus
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setOrderStatus($orderStatus)
    {
        return $this->setData(self::ORDERSTATUS, $orderStatus);
    }

    /**
     * Set ATTACHMENT
     *
     * @param string $attachment
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setAttachment($attachment)
    {
        return $this->setData(self::ATTACHMENT, $attachment);
    }

    /**
     * Set PAYMENTMETHODS
     *
     * @param string $paymentMethods
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(self::PAYMENTMETHODS, $paymentMethods);
    }

    /**
     * Set SHIPPINGMETHOD
     *
     * @param string $shippingMethod
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::SHIPPINGMETHOD, $shippingMethod);
    }

    /**
     * Set ALLPAYMENTMETHODS
     *
     * @param string $allPaymentMethods
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setAllPaymentMethods($allPaymentMethods)
    {
        return $this->setData(self::ALLPAYMENTMETHODS, $allPaymentMethods);
    }

    /**
     * Set ALLSHIPPINGMETHODS
     *
     * @param string $allShippingMethods
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setAllShippingMethods($allShippingMethods)
    {
        return $this->setData(self::ALLSHIPPINGMETHODS, $allShippingMethods);
    }
}
