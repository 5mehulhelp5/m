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
use Amasty\ImportCore\Model\ResourceModel\GetProductIdentitiesBySkus;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;

class Sku2ProductIdMultiple extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var ArgumentConverter
     */
    private $argumentConverter;

    /**
     * @var string
     */
    private $identity;

    /**
     * @var GetProductIdentitiesBySkus
     */
    private $getProductIdentitiesBySkus;

    public function __construct(
        $config,
        ArgumentConverter $argumentConverter,
        MetadataPool $metadataPool,
        GetProductIdentitiesBySkus $getProductIdentitiesBySkus
    ) {
        parent::__construct($config);
        $this->argumentConverter = $argumentConverter;
        $this->identity = $metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        $this->getProductIdentitiesBySkus = $getProductIdentitiesBySkus;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Product SKUs To Product IDs')->getText();
    }

    public function transform($value)
    {
        $identity = $this->config[ActionConfigBuilder::IDENTITY] ?? $this->identity;
        $skus = array_map('trim', explode(',', $value));
        $productIds = $this->getProductIdentitiesBySkus->execute($skus, $identity);
        if ($productIds) {
            return implode(',', $productIds);
        }

        return $value;
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
}
