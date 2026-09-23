<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Model\ResourceModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Framework\EntityManager\MetadataPool;

class GetProductIdentitiesBySkus
{
    /**
     * @var ProductResourceModel
     */
    private ProductResourceModel $resourceModel;

    /**
     * @var string
     */
    private string $identity;

    public function __construct(
        MetadataPool $metadataPool,
        ProductResourceModel $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
        $this->identity = $metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    /**
     * @param string[] $skus
     * @param string|null $identity
     * @return array<string, int>
     */
    public function execute(array $skus, ?string $identity = null): array
    {
        $identity = $identity ?? $this->identity;
        $connection = $this->resourceModel->getConnection();
        $select = $connection->select()->from($this->resourceModel->getEntityTable(), ['sku', $identity])
            ->where($connection->quoteInto('sku IN(?)', $skus))
            ->group('entity_id');
        $ids = $connection->fetchPairs($select);

        return array_merge(array_fill_keys($skus, null), $ids);
    }
}
