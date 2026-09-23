<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Utils\Type\Table\Row\Header;

use Amasty\ImportCore\Api\Config\Profile\EntitiesConfigInterface;
use Amasty\ImportExportCore\Utils\Type\Table\Row\Header\DataHandler as CoreDataHandler;

class DataHandler extends CoreDataHandler
{
    /**
     * @var string[]
     */
    private $subEntityFieldNameMap = [
        'catalog_product_entity_attribute' => 'catalog_product_attribute',
        'catalog_product_entity_review' => 'catalog_product_review',
        'catalog_product_entity_website' => 'catalog_product_website',
        'catalog_product_entity_attribute_set' => 'catalog_product_attribute_set',
        'catalog_category_entity' => 'catalog_product_category',
        'catalog_category_entity_attribute' => 'catalog_category_attribute',
        'catalog_product_entity_category_entity_relation' => 'product_category_relation',
        'catalog_product_custom_option' => 'catalog_product_custom_option',
        'catalog_product_custom_option_value' => 'catalog_product_custom_option_value',
        'catalog_product_entity_super_attribute' => 'catalog_product_super_attribute',
        'catalog_product_entity_super_attribute_label' => 'catalog_product_super_attribute_label',
        'catalog_product_entity_super_attribute_link' => 'catalog_product_super_attribute_link',
        'catalog_product_entity_super_links' => 'catalog_product_grouped_link',
        'catalog_product_entity_crosssell_links' => 'catalog_product_crosssell',
        'catalog_product_entity_related_links' => 'catalog_product_related',
        'catalog_product_entity_upsell_links' => 'catalog_product_upsell',
        'catalog_product_entity_bundle_option' => 'catalog_product_bundle_option',
        'catalog_product_entity_bundle_option_value' => 'catalog_product_bundle_option_value',
        'catalog_product_entity_bundle_selection' => 'catalog_product_bundle_selection',
        'catalog_product_entity_tier_price' => 'catalog_product_tier_price',
        'catalog_product_entity_inventory_stock_item' => 'inventory_stock_item',
        'catalog_product_entity_inventory_stock_status' => 'inventory_stock_status',
        'inventory_source_item' => 'inventory_source_item',
        'inventory_source_carrier_link' => 'inventory_source_carrier_link',
        'inventory_source_stock_link' => 'inventory_source_stock_link',
        'inventory_stock' => 'inventory_stock'
    ];

    public function getHeaderMap(EntitiesConfigInterface $fieldsConfig, array $row): array
    {
        $headerMap = $this->getHeaderMapByFieldsConfig($fieldsConfig);
        if (empty($headerMap['fields']) && !isset($headerMap['subentities'])) {
            $headerMap = $this->getHeaderMapByRow($fieldsConfig, $row);
        }

        return $headerMap;
    }

    public function getHeaderMapByRow(?EntitiesConfigInterface $fieldsConfig, array $row): array
    {
        if (empty($row)) {
            return [];
        }

        $result = [];
        $result['fields'] = [];
        foreach ($row as $key => $value) {
            if (is_array($value)) {
                $subEntityFieldConfig = $this->getEntityFieldConfigByEntityCode($fieldsConfig, $key);
                $result['subentities'][$key] = $this->getHeaderMapByRow($subEntityFieldConfig, $value[0]);
            } else {
                $result['fields'][$key] = '';
            }
        }
        $result['map'] = $fieldsConfig ? $fieldsConfig->getMap() : null;

        return $result;
    }

    public function getHeaderMapByFieldsConfig(EntitiesConfigInterface $fieldsConfig): array
    {
        $result = [];
        $result['fields'] = [];
        if ($fieldsConfig->getFields()) {
            foreach ($fieldsConfig->getFields() as $field) {
                $result['fields'][$field->getName()] = $field->getMap();
            }
        }
        if (!empty($fieldsConfig->getMap())) {
            $result['map'] = $fieldsConfig->getMap();
        }
        if ($fieldsConfig->getSubEntitiesConfig()) {
            foreach ($fieldsConfig->getSubEntitiesConfig() as $subEntityFieldConfig) {
                $subEntityFieldName =
                    isset($this->subEntityFieldNameMap[$subEntityFieldConfig->getEntityCode()])
                    ? $this->subEntityFieldNameMap[$subEntityFieldConfig->getEntityCode()]
                    : $subEntityFieldConfig->getEntityCode();
                $result['subentities'][$subEntityFieldName] =
                    $this->getHeaderMapByFieldsConfig($subEntityFieldConfig);
            }
        }

        return $result;
    }

    private function getEntityFieldConfigByEntityCode(
        ?EntitiesConfigInterface $fieldsConfig,
        string $entityCode
    ): ?EntitiesConfigInterface {
        $subEntityFieldConfigFound = null;
        if ($fieldsConfig && $fieldsConfig->getSubEntitiesConfig()) {
            foreach ($fieldsConfig->getSubEntitiesConfig() as $subEntityFieldConfig) {
                if ($subEntityFieldConfig->getEntityCode() == $entityCode) {
                    $subEntityFieldConfigFound = $subEntityFieldConfig;
                }
            }
        }

        return $subEntityFieldConfigFound;
    }
}
