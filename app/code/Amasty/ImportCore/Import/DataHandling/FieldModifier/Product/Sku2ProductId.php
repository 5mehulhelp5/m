<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier\Product;

use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ActionConfigBuilder;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Framework\EntityManager\MetadataPool;

class Sku2ProductId extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    /**
     * @var ProductResourceModel
     */
    private $resourceModel;

    /**
     * @var string
     */
    private $identity;

    /**
     * @var array
     */
    private $entityToSkuMap = [];

    public function __construct(
        $config,
        ArgumentConverter $argumentConverter,
        MetadataPool $metadataPool,
        ProductResourceModel $resourceModel
    ) {
        parent::__construct($config);
        $this->argumentConverter = $argumentConverter;
        $this->resourceModel = $resourceModel;
        $this->identity = $metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Product SKU To Product ID')->getText();
    }

    public function transform($value)
    {
        $identity = $this->config[ActionConfigBuilder::IDENTITY] ?? $this->identity;
        $productId = (string)$this->getProductIdentityBySku($value, $identity);
        if ($productId) {
            return $productId;
        }

        return null;
    }

    public function prepareArguments(FieldInterface $field, $requestData): array
    {
        $arguments = [];
        if ($entityType = $requestData[ActionConfigBuilder::IDENTITY] ?? null) {
            $arguments = $this->argumentConverter->valueToArguments(
                (string)$entityType,
                ActionConfigBuilder::IDENTITY,
                'string'
            );
        }

        return $arguments;
    }

    private function getProductIdentityBySku(string $sku, string $identity): ?string
    {
        if (!isset($this->entityToSkuMap[$identity][$sku])) {
            $connection = $this->resourceModel->getConnection();
            $select = $connection->select()->from($this->resourceModel->getEntityTable(), $identity)
                ->where('sku = :sku');
            $bind = [':sku' => $sku];
            $id = (string)$connection->fetchOne($select, $bind);
            $this->entityToSkuMap[$identity][$sku] = $id ?: null;
        }

        return $this->entityToSkuMap[$identity][$sku];
    }
}
