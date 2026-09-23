<?php
/**
 * Copyright © Cyber Solutions LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CyberSolutionsLLC\CerakoteReport\Ui\Component\Listing\Column;

use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Admin option list of cerakote_color attribute values used by the
 * report's Cerakote Color filter dropdown.
 */
class CerakoteColorOptions implements OptionSourceInterface
{
    /**
     * @var EavConfig
     */
    private EavConfig $eavConfig;

    /**
     * @param EavConfig $eavConfig
     */
    public function __construct(EavConfig $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Return the attribute's option labels as filter options.
     *
     * Values are the option labels (not ids) because the report collection
     * resolves the cerakote_color filter against the option label column.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $attribute = $this->eavConfig->getAttribute(CatalogProduct::ENTITY, 'cerakote_color');
        if ($attribute && $attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                $label = (string) $option['label'];
                if ($label === '') {
                    continue;
                }
                $options[] = [
                    'value' => $label,
                    'label' => $label,
                ];
            }
        }
        usort($options, static fn (array $left, array $right): int => strcmp($left['label'], $right['label']));
        return $options;
    }
}
