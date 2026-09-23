<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\GiftCard\ResourceModel\Price;

use Amasty\GiftCard\Model\GiftCard\Attributes;
use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard as GiftCardProduct;
use Amasty\GiftCard\Model\GiftCard\ResourceModel\GiftCardPrice;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;

class GetGiftCardPrices
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var EntityMetadataInterface
     */
    private $metadata;

    /**
     * @var string
     */
    private $connectionName;

    /**
     * @var AdapterInterface
     */
    private $connection;

    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        string $connectionName = 'default'
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadata = $metadataPool->getMetadata(ProductInterface::class);
        $this->connectionName = $connectionName;
    }

    public function setConnection(string $connection): void
    {
        $this->connectionName = $connection;
    }

    /**
     * @param string[] $entityIds
     * @return array [['product_id' => ['website_id' => $gCardPriceData], ... ], ...]
     */
    public function getGiftCardPricesData(array $entityIds = []): array
    {
        $openAmountSelect = $this->getOpenAmountPricesSelect($entityIds);
        $amountsSelect = $this->getAmountPricesSelect($entityIds);

        $connection = $this->getConnection();
        $idField = $this->metadata->getIdentifierField();
        $linkField = $this->metadata->getLinkField();

        //combining to price selects to get min\max value for each product\website
        $select = $connection->select()->from(
            ['main' => $openAmountSelect],
            [
                'product_id' => $idField,
                'website_id' => new \Zend_Db_Expr('IFNULL(website_id, 0)'),
                'min_price'  => new \Zend_Db_Expr(
                    'CASE WHEN min_price < min_value_amount OR min_value_amount IS NULL'
                    . ' THEN min_price ELSE min_value_amount END '
                ),
                'max_price'  => new \Zend_Db_Expr(
                    'CASE WHEN max_price > max_value_amount OR max_value_amount IS NULL'
                    . ' THEN max_price ELSE max_value_amount END'
                )
            ]
        )->joinLeft(
            ['amounts' => $amountsSelect],
            'main.' . $linkField . ' = amounts.product_id',
            []
        );
        $giftCardsPricesData = $connection->fetchAll($select);

        $result = [];
        foreach ($giftCardsPricesData as $giftCardPrice) {
            $result[$giftCardPrice['product_id']][$giftCardPrice['website_id']] = $giftCardPrice;
        }

        return $result;
    }

    /**
     * GiftCard open amounts are stored in attribute tables
     * retrieving them in separate select to combine in min\max price calculations
     *
     * @param string[] $entityIds
     * @return Select
     */
    private function getOpenAmountPricesSelect(array $entityIds): Select
    {
        $connection = $this->getConnection();
        $idField = $this->metadata->getIdentifierField();
        $linkField = $this->metadata->getLinkField();
        $attributeSelect = $connection->select();
        $attributeSelect->from(
            $this->getTable('eav_attribute'),
            ['attribute_id']
        )->where(
            'attribute_code IN (?)',
            [Attributes::OPEN_AMOUNT_MIN, Attributes::OPEN_AMOUNT_MAX]
        );

        $select = $connection->select()->from(
            ['e' => $this->getTable('catalog_product_entity')],
            array_unique([
                $linkField,
                $idField,
                'min_price' => new \Zend_Db_Expr('ROUND(MIN(pd.value), 2)'),
                'max_price' => new \Zend_Db_Expr('ROUND(MAX(pd.value), 2)')
            ])
        )->joinLeft(
            ['pd' => $this->getTable('catalog_product_entity_decimal')],
            'e.' . $linkField . ' = pd.' . $linkField . ' AND attribute_id IN (' . $attributeSelect . ')',
            []
        )->where(
            'type_id = ?',
            GiftCardProduct::TYPE_AMGIFTCARD
        )->group('e.' . $linkField);

        if (!empty($entityIds)) {
            $select->where(
                'e.' . $idField . ' IN (?)',
                $entityIds
            );
        }

        return $select;
    }

    /**
     * Retrieving min\max amount value for each product-website combination
     * including 'All Website' values in calculations
     *
     * @param string[] $entityIds
     * @return Select
     */
    private function getAmountPricesSelect(array $entityIds): Select
    {
        $connection = $this->getConnection();
        $linkField = $this->metadata->getLinkField();
        $idField = $this->metadata->getIdentifierField();

        $allWebsitesSelect = $connection->select();
        $allWebsitesSelect->from(
            ['e' => $this->getTable(GiftCardPrice::TABLE_NAME)]
        )->joinLeft(
            ['cpe' => $this->getTable('catalog_product_entity')],
            'e.product_id = cpe.' . $linkField,
            []
        );
        if (!empty($entityIds)) {
            $allWebsitesSelect->where(
                'cpe.' . $idField . ' IN (?)',
                $entityIds
            );
        }

        $websiteSelect = $connection->select();
        $websiteSelect->from(
            ['a' => $this->getTable(GiftCardPrice::TABLE_NAME)],
            [
                'a.price_id',
                'a.product_id',
                'b.website_id',
                'a.attribute_id',
                'a.value'
            ]
        )->joinLeft(
            ['cpe' => $this->getTable('catalog_product_entity')],
            'a.product_id = cpe.' . $linkField,
            []
        )->joinInner( //joining to add all website 0 records as website_id records
            ['b' => $this->getTable(GiftCardPrice::TABLE_NAME)],
            'a.product_id = b.product_id AND a.website_id = 0 AND b.website_id != a.website_id',
            []
        );
        if (!empty($entityIds)) {
            $websiteSelect->where(
                'cpe.' . $idField . ' IN (?)',
                $entityIds
            );
        }
        $unionSelect = $connection->select()->union(
            [$websiteSelect, $allWebsitesSelect],
            Select::SQL_UNION_ALL
        );

        return $connection->select()->from(
            $unionSelect,
            [
                'product_id',
                'website_id',
                'min_value_amount' => new \Zend_Db_Expr('MIN(value)'),
                'max_value_amount' => new \Zend_Db_Expr('MAX(value)')
            ]
        )->group(['product_id', 'website_id']);
    }

    private function getConnection(): AdapterInterface
    {
        if (null === $this->connection) {
            $this->connection = $this->resourceConnection->getConnection($this->connectionName);
        }

        return $this->connection;
    }

    private function getTable(string $tableName): string
    {
        return $this->resourceConnection->getTableName($tableName, $this->connectionName);
    }
}
