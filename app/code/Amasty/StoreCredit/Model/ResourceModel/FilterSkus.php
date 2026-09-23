<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class FilterSkus implements FilterExistingEntityInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string[] $skus
     * @return array
     */
    public function execute(array $skus): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName('catalog_product_entity'),
            'sku'
        )->where(
            'sku IN (?)',
            $skus
        );

        return $connection->fetchCol($select);
    }
}
