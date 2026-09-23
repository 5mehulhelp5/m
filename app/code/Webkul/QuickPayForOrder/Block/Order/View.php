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

namespace Webkul\QuickPayForOrder\Block\Order;

use Magento\Store\Model\StoreManagerInterface;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    
    /**
     * @var QuickPayOrdersFactory
     */
    protected $quickPayOrdersFactory;
    
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    
    protected $_quoteFactory;
    protected $_quickPayOrdersFactory;
    protected $_productFactory;
    protected $helper;
    protected $urlEncoder;
    protected $urlDecoder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory $quickPayOrdersFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Webkul\QuickPayForOrder\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory $quickPayOrdersFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\QuickPayForOrder\Helper\Data $helper,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_quoteFactory = $quoteFactory;
        $this->_quickPayOrdersFactory = $quickPayOrdersFactory;
        $this->_productFactory = $productFactory;
        $this->helper = $helper;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
    }

    /**
     * Return quote.
     *
     * @param int $quoteId
     * @return object \Magento\Quote\Model\Quote
     */
    public function getQuoteById($quoteId)
    {
        if ($quoteId != '') {
            $quote = $this->_quoteFactory->create()->load($quoteId);
            return $quote;
        }
    }
    
    /**
     * Return quickPayOrder
     *
     * @param int $payId
     * @return object \Webkul\QuickPayForOrder\Model\QuickPayOrders
     */
    public function getQuickPayOrderId($payId)
    {
        if ($payId != '') {
            $payOrder = $this->_quickPayOrdersFactory->create()->load($payId);
            return $payOrder;
        }
    }

    /**
     * Return product.
     *
     * @param int $id
     * @return object \Magento\Catalog\Model\Product
     */
    public function getProduct($id)
    {
        return $this->_productFactory->create()->load($id);
    }

    /**
     * @return object \Webkul\QuickPayForOrder\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    public function encode($url)
    {
        return $this->urlEncoder->encode($url);
    }

    public function decode($url)
    {
        return $this->urlDecoder->decode($url);
    }
}
