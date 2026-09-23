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

class CartUpdateItemBefore implements ObserverInterface
{
    
    protected $quoteFactory;
    protected $quickPayOrdersFactory;
    protected $customerSession;
    
    /**
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory $quickPayOrdersFactory
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory $quickPayOrdersFactory,
        \Magento\Customer\Model\Session $session
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->customerSession = $session;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        $quote = $cart->getQuote();
        $quoteId = $this->customerSession->getQuickPayQuoteId();
        if ($quoteId && ($quote->getId() == $quoteId)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("You are not allowed to update this quote items.")
            );
        }
    }
}
