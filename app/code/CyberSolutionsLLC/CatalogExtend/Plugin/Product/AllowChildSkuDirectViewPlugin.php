<?php

namespace CyberSolutionsLLC\CatalogExtend\Plugin\Product;

use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use CyberSolutionsLLC\CatalogExtend\Helper\ConfigurableHelper;

/**
 * Magento's own product-view gate (Helper\Product::canShow()) rejects any
 * product with Visibility = "Not Visible Individually" - which is how
 * configurable children are (correctly) kept out of category/search
 * listings. That same gate also blocks the product controller from
 * rendering the child on its own direct URL, which this task requires.
 *
 * This plugin unblocks direct rendering for enabled configurable children
 * only. It does not touch category/search collections - those filter by
 * visibility independently of canShow(), so children stay excluded there.
 */
class AllowChildSkuDirectViewPlugin
{
    /**
     * @var ConfigurableHelper
     */
    private $configurableHelper;

    /**
     * @param ConfigurableHelper $configurableHelper
     */
    public function __construct(ConfigurableHelper $configurableHelper)
    {
        $this->configurableHelper = $configurableHelper;
    }

    /**
     * @param ProductHelper $subject
     * @param bool $result
     * @param Product|int $product
     * @param string $where
     * @return bool
     */
    public function afterCanShow(ProductHelper $subject, $result, $product, $where = 'catalog')
    {
        if ($result || !$product instanceof Product || !$product->getId()) {
            return $result;
        }

        if ((int)$product->getStatus() !== (int)Status::STATUS_ENABLED) {
            return $result;
        }

        return $this->configurableHelper->isConfigurableChild($product);
    }
}
