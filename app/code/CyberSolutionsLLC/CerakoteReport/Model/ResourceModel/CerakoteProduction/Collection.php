<?php
/**
 * Copyright © Cyber Solutions LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CyberSolutionsLLC\CerakoteReport\Model\ResourceModel\CerakoteProduction;

use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface;

/**
 * Search result behind the Cerakote Production Report grid.
 *
 * Rows are order items belonging to orders in "processing" status whose
 * purchased product has a non-empty cerakote_color attribute value, joined
 * to the admin option label so the grid shows e.g. "Armor Black" instead
 * of an option id. Built on the core SearchResult so the standard UI data
 * provider, paging and export all work out of the box.
 */
class Collection extends SearchResult
{
    /**
     * @var EavConfig
     */
    private EavConfig $eavConfig;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param EavConfig $eavConfig
     * @param string $mainTable
     * @param string $resourceModel
     * @param string|null $identifierName
     * @param string|null $connectionName
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EavConfig $eavConfig,
        $mainTable = 'sales_order_item',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order\Item::class,
        $identifierName = null,
        $connectionName = null
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );
    }

    /**
     * Join order status/date, the cerakote_color attribute value and its
     * option label onto each order item row and filter to processing orders
     * with a non-empty color value.
     *
     * @return $this
     */
    protected function _initSelect()
    {
        // Explicitly select only the report fields (the parent would pull the
        // full sales_order_item row, including heavy serialized blobs).
        // item_id is aliased as entity_id because the grid data-storage
        // component indexes rows by "entity_id" by default.
        $this->getSelect()->from(
            ['main_table' => $this->getMainTable()],
            [
                'entity_id' => 'main_table.item_id',
                'item_id' => 'main_table.item_id',
                'order_id' => 'main_table.order_id',
                'product_name' => 'main_table.name',
                'sku' => 'main_table.sku',
                'qty' => 'main_table.qty_ordered',
            ]
        );

        $this->getSelect()->joinInner(
            ['so' => $this->getTable('sales_order')],
            'so.entity_id = main_table.order_id',
            ['order_date' => 'so.created_at']
        );

        $attribute = $this->eavConfig->getAttribute(CatalogProduct::ENTITY, 'cerakote_color');

        if ($attribute && $attribute->getAttributeId()) {
            $joinCondition = $this->getConnection()->quoteInto(
                'ccpei.attribute_id = ?',
                (int) $attribute->getAttributeId()
            ) . ' AND ccpei.store_id = 0 AND ccpei.entity_id = main_table.product_id';

            $this->getSelect()->joinInner(
                ['ccpei' => $this->getTable('catalog_product_entity_int')],
                $joinCondition,
                []
            )->joinInner(
                ['eaov' => $this->getTable('eav_attribute_option_value')],
                'eaov.option_id = ccpei.value AND eaov.store_id = 0',
                ['cerakote_color' => 'eaov.value']
            );
        }

        $this->getSelect()
            ->where('so.status = ?', 'processing')
            ->where('main_table.product_type IN (?)', ['simple', 'virtual', 'downloadable'])
            ->where('main_table.parent_item_id IS NULL');

        $this->addFilterToMap('cerakote_color', 'eaov.value');
        $this->addFilterToMap('order_date', 'so.created_at');
        $this->addFilterToMap('product_name', 'main_table.name');
        $this->addFilterToMap('sku', 'main_table.sku');
        $this->addFilterToMap('qty', 'main_table.qty_ordered');

        return $this;
    }

    /**
     * Group the output by Cerakote Color label, then order date, so the
     * report reads as grouped-by-color work queues.
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->getSelect()->order('eaov.value ASC');
        $this->getSelect()->order('so.created_at ASC');
        parent::_beforeLoad();
        return $this;
    }
}