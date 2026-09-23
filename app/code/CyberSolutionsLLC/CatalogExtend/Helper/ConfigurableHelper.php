<?php

namespace CyberSolutionsLLC\CatalogExtend\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ConfigurableHelper extends AbstractHelper
{
    /**
     * @var Configurable
     */
    private $configurableType;

    /**
     * @var bool[]
     */
    private $cache = [];

    public function __construct(
        Context $context,
        Configurable $configurableType
    ) {
        parent::__construct($context);
        $this->configurableType = $configurableType;
    }

    public function isConfigurableChild(\Magento\Catalog\Model\Product $product): bool
    {
        $id = $product->getId();
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = !empty($this->configurableType->getParentIdsByChild($id));
        }
        return $this->cache[$id];
    }
}
