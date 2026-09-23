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
namespace Webkul\QuickPayForOrder\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Customer\Model\Session;
use Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory;
use Webkul\QuickPayForOrder\Helper\Data;

class Save extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    
    /**
     * @var QuoteManagement
     */
    protected $quoteManagement;
    
    /**
     * @var QuickPayOrdersFactory
     */
    protected $quickPayOrdersFactory;
    
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var array
     */
    private $quoteItems = [];

    /**
     * @var mixed
     */
    private $quote = null;
    
    protected $helper;
    protected $cart;
    protected $escaper;
    
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param QuoteFactory $quoteFactory
     * @param QuoteManagement $quoteManagement
     * @param QuickPayOrdersFactory $quickPayOrdersFactory
     * @param Data $helper
     * @param Cart $cart
     * @param Session $customerSession
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        QuoteFactory $quoteFactory,
        QuoteManagement $quoteManagement,
        QuickPayOrdersFactory $quickPayOrdersFactory,
        Data $helper,
        Cart $cart,
        Session $customerSession,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteManagement = $quoteManagement;
        $this->quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->helper = $helper;
        $this->cart = $cart;
        $this->customerSession = $customerSession;
        $this->escaper = $escaper;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $resultPage = $this->resultPageFactory->create();
            $quoteId = $this->getRequest()->getParam('quote_id');
            $payId = $this->getRequest()->getParam('pay_id');
            $orderComment = $this->escaper->escapeHtml($this->getRequest()->getParam('orderComment'));
            $this->quote = $this->quoteFactory->create()->load($quoteId);
            $customerId = $this->quote->getCustomerId();
            $customerFirstName = $this->quote->getCustomerFirstname();
            $customerMiddleName = $this->quote->getCustomerMiddlename();
            $customerLastName = $this->quote->getCustomerLastname();
            $customerName = $customerFirstName.' '.$customerLastName;

            if ($customerId != $this->customerSession->getCustomerId()) {
                $loginUrl = $this->_url->getUrl(
                    'customer/account/login',
                    ['referer' => base64_encode($this->_redirect->getRefererUrl())]
                );
                $loginUrl = "<a href='".$loginUrl."'>".__("Login")."</a>";
                $message = 'Invalid user! Please %1 with %2 customer.';
                if ($this->customerSession->getCustomerId()) {
                    $message = 'Invalid customer! Please %1 with %2 customer.';
                }
                throw new LocalizedException(
                    __(
                        $message,
                        $loginUrl,
                        $customerName
                    )
                );
            }

            $quickPayOrder = $this->helper->getQuickPayOrderQuote($payId);
            $currentCurrencyCode = $this->helper->getStore()->getCurrentCurrencyCode();
            $validationFlag = $this->helper->validateQuote($this->quote);
            if ($validationFlag && $quickPayOrder->getQuoteCurrencyCode() != $currentCurrencyCode) {
                throw new LocalizedException(
                    __(
                        "Switch currency to %1 to complete this order",
                        $quickPayOrder->getQuoteCurrencyCode()
                    )
                );
            }
            
            $quoteCollection = $this->quoteFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
            foreach ($quoteCollection as $quote) {
                $quote->setIsActive(0);
            }

            $quoteCollection->save();
            $quickPayQuote = $this->quoteFactory->create()->load($quoteId);
            if ($quickPayOrder->getQuoteCurrencyCode() != $currentCurrencyCode) {
                $quickPayQuote->collectTotals();
            }
            $quickPayQuote->setIsActive(1)->save();
            $this->customerSession->setQuickPayQuoteId($quoteId);
            $this->customerSession->setQuickPayId($payId);
            if (!empty($orderComment)) {
                $this->customerSession->setQuickPayOrderComment($orderComment);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        return $resultRedirect->setPath('checkout', ['_fragment' => 'payment']);
    }
}
