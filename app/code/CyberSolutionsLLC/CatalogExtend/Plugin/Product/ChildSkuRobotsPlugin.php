<?php

namespace CyberSolutionsLLC\CatalogExtend\Plugin\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use CyberSolutionsLLC\CatalogExtend\Helper\ConfigurableHelper;
use CyberSolutionsLLC\CatalogExtend\Model\Product\Attribute\Source\ChildSkuIndexStatus;

/**
 * Per-SKU robots meta for child (configurable-associated simple) products.
 *
 * Child SKUs default to noindex,follow unless explicitly set to Index -
 * this also covers products saved before the attribute existed, since
 * anything other than the explicit "index" value is treated as noindex.
 */
class ChildSkuRobotsPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ConfigurableHelper
     */
    private $configurableHelper;

    /**
     * @param Registry $registry
     * @param RequestInterface $request
     * @param ConfigurableHelper $configurableHelper
     */
    public function __construct(
        Registry $registry,
        RequestInterface $request,
        ConfigurableHelper $configurableHelper
    ) {
        $this->registry = $registry;
        $this->request = $request;
        $this->configurableHelper = $configurableHelper;
    }

    /**
     * @param Config $subject
     * @param string $result
     * @return string
     */
    public function afterGetRobots(Config $subject, string $result): string
    {
        if ($this->request->getFullActionName() !== 'catalog_product_view') {
            return $result;
        }

        $product = $this->registry->registry('product');
        if (!$product || !$this->configurableHelper->isConfigurableChild($product)) {
            return $result;
        }

        $status = $product->getData('child_sku_index_status');

        return $status === ChildSkuIndexStatus::INDEX ? 'INDEX,FOLLOW' : 'NOINDEX,FOLLOW';
    }
}
