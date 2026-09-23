<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier\CatalogInventory;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Amasty\ImportCore\Model\ThirdParty\InventoryChecker;
use Magento\Framework\App\ResourceConnection;

class StockName2StockId extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var InventoryChecker
     */
    private $inventoryChecker;

    /**
     * @var array|null
     */
    private $map;

    public function __construct(
        ResourceConnection $connection,
        InventoryChecker $inventoryChecker,
        $config
    ) {
        parent::__construct($config);
        $this->connection = $connection;
        $this->inventoryChecker = $inventoryChecker;
    }

    public function transform($value)
    {
        if (!$this->inventoryChecker->isEnabled()) {
            return $value;
        }

        $map = $this->getMap();

        return $map[$value] ?? $value;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Stock Name to Stock Id')->getText();
    }

    private function getMap(): array
    {
        if (null === $this->map) {
            foreach ($this->getStocks() as $stock) {
                $this->map[$stock['name']] = $stock['stock_id'];
            }
        }

        return $this->map;
    }

    private function getStocks(): array
    {
        $connection = $this->connection->getConnection();
        $select = $connection->select()
            ->from($this->connection->getTableName('inventory_stock'));

        return $connection->fetchAll($select);
    }
}
