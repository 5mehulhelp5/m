<?php

namespace CyberSolutionsLLC\CatalogExtend\Plugin\Sitemap;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Sitemap\Model\ResourceModel\Catalog\Product as SitemapProductResource;
use CyberSolutionsLLC\CatalogExtend\Model\Product\Attribute\Source\ChildSkuIndexStatus;

/**
 * By default, the sitemap resource model filters its base query by
 * Visibility IN (Catalog, Search, Both) - Product.php::getCollection().
 * That filter runs before this plugin's hook fires, so it silently drops
 * every configurable child (children stay "Not Visible Individually" by
 * design, to keep them out of category/search listings). An OR condition
 * added on top of an already-failed AND chain cannot rescue them.
 *
 * So instead of just adding a condition, this rebuilds the WHERE clause:
 * non-children keep going through the engine's original filter untouched,
 * while children get their own independent, visibility-blind eligibility
 * check (Enabled status + Child SKU Index Status = Index).
 */
class ExcludeConfigurableChildrenPlugin
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @param ResourceConnection $resourceConnection
     * @param EavConfig $eavConfig
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        EavConfig $eavConfig
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->eavConfig = $eavConfig;
    }

    public function afterPrepareSelectStatement(
        SitemapProductResource $subject,
        Select $result
    ): Select {
        $connection = $this->resourceConnection->getConnection();
        $indexAttribute = $this->eavConfig->getAttribute(Product::ENTITY, 'child_sku_index_status');
        $statusAttribute = $this->eavConfig->getAttribute(Product::ENTITY, 'status');

        // Preserve whatever the engine's own filters (visibility, status, ...) already
        // built up, so non-child products keep behaving exactly as before.
        $originalWhere = implode(' ', $result->getPart(Select::WHERE));
        if ($originalWhere === '') {
            $originalWhere = '1';
        }
        $result->reset(Select::WHERE);

        $result->joinLeft(
            ['cpsl' => $subject->getTable('catalog_product_super_link')],
            'cpsl.product_id = e.entity_id',
            []
        )->joinLeft(
            ['child_sku_index' => $indexAttribute->getBackend()->getTable()],
            $connection->quoteInto(
                'child_sku_index.entity_id = e.entity_id AND child_sku_index.attribute_id = ?',
                $indexAttribute->getId()
            ) . ' AND ' . $connection->quoteInto('child_sku_index.store_id = ?', 0),
            []
        )->joinLeft(
            ['child_status' => $statusAttribute->getBackend()->getTable()],
            $connection->quoteInto(
                'child_status.entity_id = e.entity_id AND child_status.attribute_id = ?',
                $statusAttribute->getId()
            ) . ' AND ' . $connection->quoteInto('child_status.store_id = ?', 0),
            []
        );

        $childIndexCondition = $connection->quoteInto('child_sku_index.value = ?', ChildSkuIndexStatus::INDEX)
            . ' AND ' . $connection->quoteInto('child_status.value = ?', Status::STATUS_ENABLED);

        $result->where(
            "(cpsl.product_id IS NULL AND ($originalWhere)) OR (cpsl.product_id IS NOT NULL AND $childIndexCondition)"
        );

        return $result;
    }
}
