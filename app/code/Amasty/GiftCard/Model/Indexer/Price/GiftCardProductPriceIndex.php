<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Indexer\Price;

use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\GiftCard\Product\Type\GiftCard as GiftCardProduct;
use Amasty\GiftCard\Model\GiftCard\ResourceModel\Price\GetGiftCardPrices;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\IndexTableStructure;
use Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\PriceModifierInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Module\Manager;

class GiftCardProductPriceIndex implements PriceModifierInterface
{
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var string
     */
    private $connectionName;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var EntityMetadataInterface
     */
    private $metadata;

    /**
     * @var GetGiftCardPrices
     */
    private $getGiftCardPrices;

    public function __construct(
        ResourceConnection $resourceConnection,
        Manager $moduleManager,
        ConfigProvider $configProvider,
        MetadataPool $metadataPool,
        GetGiftCardPrices $getGiftCardPrices,
        $connectionName = 'indexer'
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->moduleManager = $moduleManager;
        $this->configProvider = $configProvider;
        $this->metadata = $metadataPool->getMetadata(ProductInterface::class);
        $this->connectionName = $connectionName;
        $this->getGiftCardPrices = $getGiftCardPrices;
        $this->getGiftCardPrices->setConnection($connectionName);
    }

    /**
     * @param IndexTableStructure $priceTable
     * @param string[] $entityIds
     * @return void
     */
    public function modifyPrice(IndexTableStructure $priceTable, array $entityIds = []): void
    {
        if (!$this->moduleManager->isEnabled('Amasty_GiftCard')
            || !$this->configProvider->isEnabled()
            || !$this->hasGiftCardIds($entityIds)
        ) {
            return;
        }

        $gCardPricesData = $this->getGiftCardPrices->getGiftCardPricesData($entityIds);
        if (empty($gCardPricesData)) {
            return;
        }

        $result = $this->prepareGiftCardData($gCardPricesData);
        $this->getConnection()
            ->insertOnDuplicate(
                $this->getTable('catalog_product_index_price_temp'),
                $result,
                ['entity_id', 'website_id', 'customer_group_id', 'max_price', 'min_price', 'final_price', 'price']
            );
    }

    public function _resetState(): void
    {
        $this->connection = null;
    }

    /**
     * @param string[] $ids
     * @return bool
     */
    private function hasGiftCardIds(array $ids): bool
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('catalog_product_entity'),
            ['entity_id']
        )->where(
            $this->metadata->getIdentifierField() . ' IN (?)',
            $ids
        )->where(
            'type_id = ?',
            GiftCardProduct::TYPE_AMGIFTCARD
        );

        return !empty($this->getConnection()->fetchCol($select));
    }

    /**
     * @param array $gCardPricesData [['product_id' => ['website_id' => $gCardPriceData], ... ], ...]
     * @return array [[entity_id, website_id, customer_group_id, price, final_price, min_price, max_price], ...]
     */
    private function prepareGiftCardData(array $gCardPricesData): array
    {
        $customerGroupsIds = $this->getCustomerGroupsIds();
        $websiteIds = $this->getWebsiteIds();

        $websitePrices = [];
        foreach ($gCardPricesData as $productId => $gCardWebsiteData) {
            foreach ($websiteIds as $websiteId) {
                if (!isset($gCardWebsiteData[$websiteId]) && !isset($gCardWebsiteData[0])) {
                    continue;
                }
                $minPrice = $gCardWebsiteData[$websiteId]['min_price'] ?? $gCardWebsiteData[0]['min_price'];
                $websitePrices[] = [
                    'entity_id' => $productId,
                    'website_id' => $websiteId,
                    'price' => $minPrice,
                    'final_price' => $minPrice,
                    'min_price' => $minPrice,
                    'max_price' => $gCardWebsiteData[$websiteId]['max_price'] ?? $gCardWebsiteData[0]['max_price']
                ];
            }
        }

        $result = [];
        foreach ($customerGroupsIds as $groupId) {
            foreach ($websitePrices as $gCardPrice) {
                $gCardPrice['customer_group_id'] = $groupId;
                $result[] = $gCardPrice;
            }
        }

        return $result;
    }

    /**
     * @return string[]
     */
    private function getCustomerGroupsIds(): array
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('customer_group'),
            ['customer_group_id']
        );

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @return string[]
     */
    private function getWebsiteIds(): array
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('store_website'),
            ['website_id']
        )->where('website_id != ?', 0);

        return $this->getConnection()->fetchCol($select);
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
