<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\Product;

use Amasty\ExportCore\Api\Config\Profile\FieldInterface;
use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ActionConfigBuilder;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Amasty\ExportCore\Export\Utils\Config\ArgumentConverter;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

class ProductId2SkuMultiple extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    /**
     * @var string
     */
    private $identity;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        $config,
        ArgumentConverter $argumentConverter
    ) {
        parent::__construct($config);
        $this->resourceConnection = $resourceConnection;
        $this->argumentConverter = $argumentConverter;
        $this->identity = $metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    public function getLabel(): string
    {
        return __('Product IDs To Product SKUs')->getText();
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function transform($value)
    {
        if (!empty($value)) {
            $productIds = array_map('trim', explode(',', $value));
            $identity = $this->config[ActionConfigBuilder::IDENTITY] ?? $this->identity;
            $skus = $this->getSkusByProductIdentity($productIds, $identity);
            if ($skus) {
                return implode(',', array_filter($skus));
            }
        }

        return $value;
    }

    public function prepareArguments(FieldInterface $field, $requestData): array
    {
        $arguments = [];
        if ($optionSource = $requestData[ActionConfigBuilder::IDENTITY] ?? null) {
            $arguments = $this->argumentConverter->valueToArguments(
                (string)$optionSource,
                ActionConfigBuilder::IDENTITY,
                'string'
            );
        }

        return $arguments;
    }

    /**
     * @param string[] $productIds
     * @param string $identity
     * @return string[]
     */
    private function getSkusByProductIdentity(array $productIds, string $identity): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('catalog_product_entity'), 'sku')
            ->where($connection->quoteInto($identity . ' IN(?)', $productIds));

        return $connection->fetchCol($select);
    }
}
