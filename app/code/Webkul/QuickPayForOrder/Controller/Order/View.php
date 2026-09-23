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
use Magento\Store\Model\StoreManagerInterface;
use Webkul\QuickPayForOrder\Helper\Data;

class View extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;

    /**
     * @var Data
     */
    protected $helper;
    
    protected $_resultPageFactory;
    protected $urlEncoder;
    protected $urlDecoder;
    
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helper,
        StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Url\DecoderInterface $urlDecoder
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->helper = $helper;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $quoteId = $this->urlDecoder->decode($this->getRequest()->getParam('id'));
            $payId = $this->urlDecoder->decode($this->getRequest()->getParam('payid'));
            $quote = $this->helper->getQuoteById($quoteId);

            if ($quote && $quote->getId()) {
                if ($this->helper->getStore()->getId() != $quote->getStoreId()) {
                    $this->_storeManagerInterface->setCurrentStore($quote->getStoreId());
                    $currentUrl = $this->_url->getUrl(
                        '*/*/*',
                        $this->getRequest()->getParams()
                    );
                    $path = $this->_url->getUrl(
                        'stores/store/redirect',
                        [
                            '___store' => $quote->getStore()->getCode(),
                            '___from_store' => $this->helper->getStore()->getCode(),
                            'uenc' => $this->urlEncoder->encode($currentUrl)
                        ]
                    );
                    return $resultRedirect->setPath($path);
                }
            } else {
                $this->messageManager->addError(__("Quote not found."));
                return $resultRedirect->setPath($this->_redirect->getRefererUrl());
            }

            $quickPayInfo = $this->helper->getQuickPayOrderQuote($payId);
            $currentCurrencyCode = $this->_storeManagerInterface->getStore()->getCurrentCurrencyCode();
            $validationFlag = $this->helper->validateQuote($quote);
            if ($validationFlag && $quickPayInfo->getQuoteCurrencyCode() != $currentCurrencyCode) {
                $this->messageManager->addWarning(
                    __("You can complete this order by using %1 currency only.", $quickPayInfo->getQuoteCurrencyCode())
                );
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
