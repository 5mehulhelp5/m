<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\CatalogInventory;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Model\ThirdParty\InventoryChecker;
use Magento\Framework\App\ResourceConnection;

class StockId2StockName extends AbstractModifier implements FieldModifierInterface
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

        return $map[(int)$value] ?? $value;
    }

    public function getLabel(): string
    {
        return __('Stock Id To Stock Name')->getText();
    }

    private function getMap(): array
    {
        if (null === $this->map) {
            foreach ($this->getStocks() as $stock) {
                $this->map[$stock['stock_id']] = $stock['name'];
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
