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
namespace Webkul\QuickPayForOrder\Block\Adminhtml\Order\Create\Items;

class ItemsGrid extends \Magento\Backend\Block\Template
{
    
    protected $helper;
    protected $magentoHelper;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\QuickPayForOrder\Helper\Data $helper,
        \Magento\Catalog\Helper\Data $magentoHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->magentoHelper = $magentoHelper;
        return parent::__construct($context, $data);
    }

    public function getHelper()
    {
        return $this->helper;
    }
    
    public function getMagentoHelper()
    {
        return $this->magentoHelper;
    }
}
