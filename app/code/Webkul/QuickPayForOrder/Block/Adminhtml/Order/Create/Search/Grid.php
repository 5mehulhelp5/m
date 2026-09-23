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
namespace Webkul\QuickPayForOrder\Block\Adminhtml\Order\Create\Search;

/**
 * Adminhtml sales order create search products block
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{
    /**
     * Prepare collection to be displayed in the grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $attributes = $this->_catalogConfig->getProductAttributes();
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productFactory->create()->getCollection();
        $collection->setStore(
            $this->getStore()
        )->addAttributeToSelect(
            $attributes
        )->addAttributeToSelect(
            'sku'
        )->addStoreFilter()->addAttributeToFilter(
            'type_id',
            $this->_salesConfig->getAvailableProductTypes()
        )->addAttributeToSelect(
            'gift_message_available'
        )->addAttributeToFilter(
            'sku',
            ['neq' => 'wk_service']
        );

        $this->setCollection($collection);
        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareCollection();
    }
}
