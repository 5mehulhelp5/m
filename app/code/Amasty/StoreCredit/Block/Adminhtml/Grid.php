<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Template
{
    public function toHtml()
    {
        return $this->getChildHtml('amstorecredit-history');
    }
}
