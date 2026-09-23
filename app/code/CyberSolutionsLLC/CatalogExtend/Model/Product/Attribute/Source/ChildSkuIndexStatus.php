<?php

namespace CyberSolutionsLLC\CatalogExtend\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Options for the "Child SKU Index Status" product attribute.
 */
class ChildSkuIndexStatus extends AbstractSource
{
    public const NOINDEX = 'noindex';
    public const INDEX = 'index';

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::NOINDEX, 'label' => __('Noindex')],
                ['value' => self::INDEX, 'label' => __('Index')],
            ];
        }
        return $this->_options;
    }
}
