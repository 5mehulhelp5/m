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
namespace Webkul\QuickPayForOrder\Plugin\Controller\Order;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Framework\App\RequestInterface;
use Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Webkul\QuickPayForOrder\Helper\Data;

class Reorder
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    
    protected $_coreRegistry;
    protected $orderLoader;
    protected $request;
    protected $_quickPayOrdersFactory;
    protected $quoteRepository;
    protected $helper;
    
    /**
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param Registry $registry
     * @param OrderLoaderInterface $orderLoader
     * @param RequestInterface $request
     * @param QuickPayOrdersFactory $quickPayOrdersFactory
     */
    public function __construct(
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        Registry $registry,
        OrderLoaderInterface $orderLoader,
        RequestInterface $request,
        QuickPayOrdersFactory $quickPayOrdersFactory,
        CartRepositoryInterface $quoteRepository,
        Data $helper
    ) {
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->_coreRegistry = $registry;
        $this->orderLoader = $orderLoader;
        $this->request = $request;
        $this->_quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->quoteRepository = $quoteRepository;
        $this->helper = $helper;
    }

    /**
     * @see \Magento\Sales\Controller\Order\Reorder::execute()
     */
    public function aroundExecute(\Magento\Sales\Controller\Order\Reorder $subject, \Closure $proceed)
    {
        $result = $this->orderLoader->load($this->request);
        $order = $this->_coreRegistry->registry('current_order');
        $this->_coreRegistry->unregister('current_order');
        if ($order) {
            $quoteId = $order->getQuoteId();

            $collection = $this->_quickPayOrdersFactory->create()->getCollection()
                ->addFieldToFilter('quote_id', $quoteId)
                ->addFieldToFilter('order_id', $order->getRealOrderId());

            if ($collection->getSize() > 0) {
                $quoteId = $order->getQuoteId();
                $quote = $this->quoteRepository->get($quoteId);
                if ($this->helper->validateQuote($quote)) {
                    $this->messageManager->addError(__('Not allowed to reorder %1', $order->getIncrementId()));
                    return $this->resultRedirectFactory->create()->setPath('*/*/history');
                }
            }
        }

        return $proceed();
    }
}
