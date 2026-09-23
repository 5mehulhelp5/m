<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\QuickPayForOrder\Plugin\Controller\Product;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;

class Edit
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @param ProductFactory $productFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ProductFactory $productFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager
    ) {
        $this->productFactory = $productFactory;
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @see \Magento\Catalog\Controller\Product\Edit::execute()
     */
    public function afterExecute(\Magento\Catalog\Controller\Adminhtml\Product\Edit $subject, $result)
    {
        $productId = (int) $subject->getRequest()->getParam('id');
        $product = $this->productFactory->create()->load($productId);
        if ($product->getSku() == \Webkul\QuickPayForOrder\Helper\Data::SKU) {
            $this->messageManager->addNotice(__('Not allowed to edit service product.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        return $result;
    }
}
