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
namespace Webkul\QuickPayForOrder\Api\Data;

/**
 * QuickPay Order History interface.
 * @api
 */
interface QuickPayOrderInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITYID          = 'entity_id';
    const QUOTEID           = 'quote_id';
    const CUSTOMERID        = 'customer_id';
    const CREATEDAT         = 'created_at';
    const ORDERSTATUS       = 'order_status';
    const ORDERID           = 'order_id';
    const ATTACHMENT        = 'attachment';
    const PAYMENTMETHODS    = 'payment_methods';
    const SHIPPINGMETHOD    = 'shipping_method';
    const ALLPAYMENTMETHODS = 'all_payment_methods';
    const ALLSHIPPINGMETHODS = 'all_shipping_methods';
    /**#@-*/

    /**
     * Get entity_id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Get quote_id
     *
     * @return int
     */
    public function getQuoteId();
    
    /**
     * Get customer_id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Get created_at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Get order_id
     *
     * @return int
     */
    public function getOrderId();
    
    /**
     * Get order_status
     *
     * @return string|null
     */
    public function getOrderStatus();

    /**
     * Get attachment
     *
     * @return string|null
     */
    public function getAttachment();

    /**
     * Get payment_methods
     *
     * @return string|null
     */
    public function getPaymentMethods();

    /**
     * Get shipping_method
     *
     * @return string|null
     */
    public function getShippingMethod();

    /**
     * Get all_payment_methods
     *
     * @return string|null
     */
    public function getAllPaymentMethods();

    /**
     * Get all_shipping_methods
     *
     * @return string|null
     */
    public function getAllShippingMethods();

    /**
     * Set id
     *
     * @param int $id
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setEntityId($id);

    /**
     * Set quote_id
     *
     * @param int $quoteId
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setQuoteId($quoteId);
    
    /**
     * Set customer_id
     *
     * @param int $customerId
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setCustomerId($customerId);

    /**
     * Set created_at
     *
     * @param string $createdAt
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set order_id
     *
     * @param int $orderId
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setOrderId($orderId);
    
    /**
     * Set order_status
     *
     * @param string $orderStatus
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setOrderStatus($orderStatus);

    /**
     * Set attachment
     *
     * @param string $attachment
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setAttachment($attachment);

    /**
     * Set payment_methods
     *
     * @param string $paymentMethods
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setPaymentMethods($paymentMethods);

    /**
     * Set shipping_method
     *
     * @param string $shippingMethod
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setShippingMethod($shippingMethod);

    /**
     * Set all_payment_methods
     *
     * @param string $allPaymentMethods
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setAllPaymentMethods($allPaymentMethods);

    /**
     * Set all_shipping_methods
     *
     * @param string $allShippingMethods
     * @return \Webkul\QuickPayForOrder\Api\Data\QuickPayOrderInterface
     */
    public function setAllShippingMethods($allShippingMethods);
}
