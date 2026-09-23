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
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

class CheckoutCartProductAddBefore implements ObserverInterface
{
    protected $customerSession;
    protected $cart;
    protected $messageManager;
    protected $cartManagement;
    protected $quoteRepository;
    protected $storeManager;
    
    /**
     * @param \Magento\Customer\Model\Session $session
     * @param Cart $cart
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        Cart $cart,
        ManagerInterface $messageManager,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $quoteRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $session;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->cartManagement = $cartManagement;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $this->cart->getQuote();
        $quoteId = $this->customerSession->getQuickPayQuoteId();

        if ($quoteId && ($quote->getId() == $quoteId)) {
            $itemsCount = count($quote->getAllVisibleItems());
            if ($itemsCount == 0) {
                $this->resetCart($quoteId);
            } else {
                $observer->getRequest()->setParam('product', false);
                $this->messageManager->addErrorMessage(__("You are not able to add items in this quote"));
            }
        }
    }

    /**
     * set new quote in cart
     */
    private function resetCart($quoteId)
    {
        $quoteId = $this->cartManagement->createEmptyCart();
        $customerId = $this->customerSession->getCustomer()->getId();
        $storeId = $this->storeManager->getStore()->getStoreId();
        $this->cartManagement->assignCustomer($quoteId, $customerId, $storeId);
        $quote = $this->quoteRepository->get($quoteId);
        $this->cart->setQuote($quote);
    }
}
