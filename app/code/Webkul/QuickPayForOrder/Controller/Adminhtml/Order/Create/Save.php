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
namespace Webkul\QuickPayForOrder\Controller\Adminhtml\Order\Create;

use Magento\Framework\Exception\PaymentException;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Webkul\QuickPayForOrder\Helper\Data;
use Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Sales\Controller\Adminhtml\Order\Create\Save
{
    /**
     * @var \Webkul\QuickPayForOrder\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory
     */
    protected $quickPayOrdersFactory;
    
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;
    
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Payment methods
     *
     * @var string
     */
    private $quickPayMethods;

    /**
     * ALl Payment methods
     *
     * @var int
     */
    private $showAllPayments = 0;

    /**
     * All Shipping methods
     *
     * @var int
     */
    private $showAllShippings = 0;
    
    /**
     * @param Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Data $helper
     * @param QuickPayOrdersFactory $quickPayOrdersFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Data $helper,
        QuickPayOrdersFactory $quickPayOrdersFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct(
            $context,
            $productHelper,
            $escaper,
            $resultPageFactory,
            $resultForwardFactory
        );
        $this->helper = $helper;
        $this->quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->date = $date;
    }

    /**
     * Saving quote and create order
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $order = $this->getRequest()->getPost('order');
            if (!empty($order['send_quick_pay_link']) && $order['send_quick_pay_link'] == 1) {
                $this->quickPayMethods = implode(',', $this->getRequest()->getParams()['quickpay_methods']);
                $this->getRequest()->setParam('payment', ['method' => 'checkmo']);
                if (!empty($order['allow_all_payment'])) {
                    $this->showAllPayments = 1;
                }
                if (!empty($order['allow_all_shipping'])) {
                    $this->showAllShippings = 1;
                }
            }

            // check if the creation of a new customer is allowed
            if (!$this->_authorization->isAllowed('Magento_Customer::manage')
                && !$this->_getSession()->getCustomerId()
                && !$this->_getSession()->getQuote()->getCustomerIsGuest()
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }

            $createCustomer = false;

            if ($this->_getSession()->getQuote()->getCustomerId()) {
                $quoteCustomer = $this->helper->getCustomerById($this->_getSession()->getQuote()->getCustomerId());
                $customerWebsiteId = $quoteCustomer->getWebsiteId();
                $quoteStore = $this->helper->getStoreById($this->_getSession()->getQuote()->getStoreId());
                $quoteWebsiteId = $quoteStore->getWebsiteId();

                if ($quoteWebsiteId != $customerWebsiteId) {
                    $createCustomer = true;
                }
            }

            $this->_getOrderCreateModel()->getQuote()->setCustomerId($this->_getSession()->getCustomerId());
            $this->_processActionData('save');
            $paymentData = $this->getRequest()->getPost('payment');

            $billingAddress = '';
            $shippingAddress = '';
            if (isset($order['billing_address'])) {
                $billingAddress = $order['billing_address'];
            }
            if (isset($order['shipping_address'])) {
                $shippingAddress = $order['shipping_address'];
            }
            if ($order['send_quick_pay_link'] == 1) {
                $quoteItems = $this->_getSession()->getQuote()->getAllItems();
                if (empty($quoteItems)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("No item found in order.")
                    );
                }
                if (!$this->_getSession()->getQuote()->getCustomerId() || $createCustomer) {
                    $store = $this->_getSession()->getStore();
                    $customer = $this->helper->createCustomer($this->_getSession()->getQuote(), $store);
                    if ($customer) {
                        $customer2 = $this->helper->getCustomer($customer);
                        $this->_getSession()->getQuote()->assignCustomer($customer2);
                        $address = ($billingAddress == '') ? $shippingAddress : $billingAddress;
                        $this->_getSession()->getQuote()->getBillingAddress()->addData($address);
                        $address = ($shippingAddress == '') ? $billingAddress : $shippingAddress;
                        $this->_getSession()->getQuote()->getShippingAddress()->addData($address);
                    }
                }
                $this->_getOrderCreateModel()->saveQuote();
                $quickPayId = $this->saveQuoteData($this->_getSession()->getQuote());
                if ($quickPayId) {
                    $this->helper->sendQuickPayEmail($this->_getSession()->getQuote(), $quickPayId);
                }
                $this->_getSession()->clearStorage();
                $this->messageManager->addSuccess(
                    __("Quick Pay link has been sent successfully at customer's email.")
                );
                $resultRedirect->setPath('sales/order/index');
            } else {
                if ($paymentData) {
                    $paymentData['checks'] = $this->getChecks();
                    $this->_getOrderCreateModel()->setPaymentData($paymentData);
                    $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
                }
                $order = $this->_getOrderCreateModel()
                    ->setIsValidate(true)
                    ->importPostData($this->getRequest()->getPost('order'))
                    ->createOrder();
                $this->_getSession()->clearStorage();
                $this->messageManager->addSuccess(__('You created the order.'));
                if ($this->_authorization->isAllowed('Magento_Sales::actions_view')) {
                    $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
                } else {
                    $resultRedirect->setPath('sales/order/index');
                }
            }
        } catch (PaymentException $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('sales/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // customer can be created before place order flow is completed and should be stored in current session
            $this->_getSession()->setCustomerId($this->_getSession()->getQuote()->getCustomerId());
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('sales/*/');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Order saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('sales/*/');
        }
        return $resultRedirect;
    }

    /**
     * save quote data in quickPayOrders model
     *
     * @param \Magento\Backend\Model\Session\Quote $quote
     * @return int
     */
    private function saveQuoteData($quote)
    {
        $attachment = $this->uploadAttachment();
        $quickPayOrder = $this->quickPayOrdersFactory->create();
        $quickPayOrder->setQuoteId($quote->getId())
            ->setCustomerId($quote->getCustomerId())
            ->setCreatedAt($this->date->gmtDate())
            ->setAttachment($attachment)
            ->setPaymentMethods($this->quickPayMethods)
            ->setShippingMethod($quote->getShippingAddress()->getShippingMethod())
            ->setAllPaymentMethods($this->showAllPayments)
            ->setAllShippingMethods($this->showAllShippings)
            ->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
        $quickPayOrder->save();
        return $quickPayOrder->getId();
    }

    /**
     * upload attachment file and return file path
     *
     * @return null|string
     */
    private function uploadAttachment()
    {
        $files = $this->getRequest()->getFiles('order');
        $filePath = '';
        if ($files['custom_attachment']['name'] !== '') {
            $uploader = $this->fileUploaderFactory->create(['fileId' => 'order[custom_attachment]']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf']);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $path = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath('attachments/');
            $result = $uploader->save($path);
            $filePath = 'attachments/'.$result['file'];
        }
        return $filePath;
    }

    /**
     * @return array
     */
    private function getChecks()
    {
        return  [
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_INTERNAL,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
        ];
    }
}
