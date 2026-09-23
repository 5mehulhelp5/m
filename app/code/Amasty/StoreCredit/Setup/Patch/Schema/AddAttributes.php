<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Setup\Patch\Schema;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;

class AddAttributes implements SchemaPatchInterface
{
    /**
     * @var SalesSetup
     */
    private $salesSetup;

    /**
     * @var QuoteSetup
     */
    private $quoteSetup;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array<string, string>
     */
    private $flatEntityTables = [
        Order::ENTITY       => 'sales_order',
        'order_item'        => 'sales_order_item',
        'quote'             => 'quote',
        'quote_item'        => 'quote_item',
        'invoice'           => 'sales_invoice',
        'invoice_item'      => 'sales_invoice_item',
        'creditmemo'        => 'sales_creditmemo',
        'creditmemo_item'   => 'sales_creditmemo_item'
    ];

    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->salesSetup = $salesSetupFactory->create();
        $this->quoteSetup = $quoteSetupFactory->create();
        $this->resourceConnection = $resourceConnection;
    }

    public function apply()
    {
        $this->addOrderAttributes();
        $this->addQuoteAttributes();
        $this->addInvoiceAttributes();
        $this->addCreditmemoAttributes();

        return $this;
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }

    private function addOrderAttributes(): void
    {
        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_BASE_AMOUNT,
            ['type' => 'decimal']
        );

        $this->addAttributeIfNotExists(Order::ENTITY, SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);
        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_INVOICED_BASE_AMOUNT,
            ['type' => 'decimal']
        );

        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_INVOICED_AMOUNT,
            ['type' => 'decimal']
        );

        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_REFUNDED_BASE_AMOUNT,
            ['type' => 'decimal']
        );

        $this->addAttributeIfNotExists(Order::ENTITY, SalesFieldInterface::AMSC_REFUNDED_AMOUNT, [
            'type' => 'decimal',
            'grid' => true
        ]);

        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_SHIPPING_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );

        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_SHIPPING_AMOUNT_INVOICED,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );

        $this->addAttributeIfNotExists(
            Order::ENTITY,
            SalesFieldInterface::AMSC_SHIPPING_AMOUNT_REFUNDED,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );

        $this->addAttributeIfNotExists(
            'order_item',
            SalesFieldInterface::AMSC_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );
    }

    private function addQuoteAttributes(): void
    {
        $this->addAttributeIfNotExists('quote', SalesFieldInterface::AMSC_USE, ['type' => 'boolean']);
        $this->addAttributeIfNotExists('quote', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $this->addAttributeIfNotExists('quote', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);
        $this->addAttributeIfNotExists(
            'quote',
            SalesFieldInterface::AMSC_SHIPPING_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );

        $this->addAttributeIfNotExists(
            'quote_item',
            SalesFieldInterface::AMSC_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );
    }

    private function addInvoiceAttributes(): void
    {
        $this->addAttributeIfNotExists('invoice', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $this->addAttributeIfNotExists('invoice', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);
        $this->addAttributeIfNotExists(
            'invoice_item',
            SalesFieldInterface::AMSC_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );

        $this->addAttributeIfNotExists(
            'invoice',
            SalesFieldInterface::AMSC_SHIPPING_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );
    }

    private function addCreditmemoAttributes(): void
    {
        $this->addAttributeIfNotExists('creditmemo', SalesFieldInterface::AMSC_BASE_AMOUNT, ['type' => 'decimal']);
        $this->addAttributeIfNotExists('creditmemo', SalesFieldInterface::AMSC_AMOUNT, ['type' => 'decimal']);

        $this->addAttributeIfNotExists(
            'creditmemo_item',
            SalesFieldInterface::AMSC_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );

        $this->addAttributeIfNotExists(
            'creditmemo',
            SalesFieldInterface::AMSC_SHIPPING_AMOUNT,
            [
                'type'      => Table::TYPE_DECIMAL,
                'visible'   => false,
                'nullable'  => true
            ]
        );
    }

    /**
     * @param string $entityTypeCode
     * @param string $attributeCode
     * @param array $params
     * @return void
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function addAttributeIfNotExists(
        string $entityTypeCode,
        string $attributeCode,
        array $params
    ): void {
        $tableName = $this->resourceConnection->getTableName($this->flatEntityTables[$entityTypeCode]);
        $connection = $this->resourceConnection->getConnection();

        if (!$connection->tableColumnExists($tableName, $attributeCode)) {
            if ($entityTypeCode === 'quote' || $entityTypeCode === 'quote_item') {
                $this->quoteSetup->addAttribute($entityTypeCode, $attributeCode, $params);
            } else {
                $this->salesSetup->addAttribute($entityTypeCode, $attributeCode, $params);
            }
        }
    }
}
