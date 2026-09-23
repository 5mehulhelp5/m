<?php

namespace CyberSolutionsLLC\SimpleToConfigRedirect\Observer;

/**
 *
 */
class CatalogProductViewPreDispatch implements \Magento\Framework\Event\ObserverInterface {
    
    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableType;
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->actionFlag = $actionFlag;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
        $this->_catalogSession = $catalogSession;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $request = $observer->getEvent()->getRequest();
        if ($request->isXmlHttpRequest() || $request->isPost()) {
            return;
        }
        
        $categoryId = (int) $request->getParam('category', false);
        $productId = (int) $request->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            
            if (in_array($websiteId, $product->getWebsiteIds())
                && ($product->getTypeId() === \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ) {
                $parents = $this->configurableType->getParentIdsByChild($product->getId());
                if (count($parents)) {
                    $parentId = $parents[0];
                    try {
                        $configurableProduct = $this->productRepository->getById($parentId, false, $storeId);
                        
                        if (in_array($websiteId, $configurableProduct->getWebsiteIds())
                            && ($configurableProduct->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
                            && $configurableProduct->isVisibleInCatalog()
                            && (((int)$configurableProduct->getVisibility()) === \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                        ) {
                            // Load configurable product current category
                            if (! $categoryId) {
                                $lastId = $this->_catalogSession->getLastVisitedCategoryId();
                                if ($configurableProduct->canBeShowInCategory($lastId)) {
                                    $categoryId = $lastId;
                                }
                            } elseif (! $configurableProduct->canBeShowInCategory($categoryId)) {
                                $categoryId = null;
                            }

                            if ($categoryId) {
                                try {
                                    $category = $this->categoryRepository->get($categoryId);
                                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                                    $category = null;
                                }
                                if ($category) {
                                    $configurableProduct->setCategory($category);
                                }
                            }
                            $controllerAction = $observer->getEvent()->getControllerAction();
                            $productUrl = $configurableProduct->getProductUrl();
                            $queryString = trim((string)$request->getServer('QUERY_STRING'));
                            if ($queryString) {
                                $productUrl .= '?' . $queryString;
                            }
                            $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
                            $controllerAction->getResponse()->setRedirect($productUrl, 301)->sendResponse();
                        }
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                        
                    }
                }
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            
        }
    }
    
}
