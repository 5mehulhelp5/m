<?php

namespace CyberSolutionsLLC\CatalogExtend\ViewModel\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class SeoCategoryLinks implements ArgumentInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array<int, array{top: Category, sub: ?Category}|null>
     */
    private $cache = [];

    public function __construct(
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    public function getCurrentProduct(): ?Product
    {
        $product = $this->registry->registry('current_product');
        return $product instanceof Product ? $product : null;
    }

    /**
     * @return array{top: Category, sub: ?Category}|null
     */
    public function getLinks(): ?array
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return null;
        }

        $pid = (int) $product->getId();
        if (array_key_exists($pid, $this->cache)) {
            return $this->cache[$pid];
        }

        try {
            $store = $this->storeManager->getStore();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->cache[$pid] = null;
        }

        $storeId = (int) $store->getId();
        $rootCategoryId = (int) $store->getRootCategoryId();

        $ids = $product->getCategoryIds();
        if (empty($ids)) {
            return $this->cache[$pid] = null;
        }

        $chosen = null;
        foreach ($ids as $id) {
            try {
                $category = $this->categoryRepository->get((int) $id, $storeId);
            } catch (NoSuchEntityException $e) {
                continue;
            }

            if (!$category->getIsActive()) {
                continue;
            }

            $path = explode('/', (string) $category->getPath());
            if (!in_array((string) $rootCategoryId, $path, true)) {
                continue;
            }

            $chosen = $category;
            break;
        }

        if (!$chosen) {
            return $this->cache[$pid] = null;
        }

        $path = array_map('intval', explode('/', (string) $chosen->getPath()));
        $rootIndex = array_search($rootCategoryId, $path, true);
        if ($rootIndex === false || !isset($path[$rootIndex + 1])) {
            return $this->cache[$pid] = null;
        }

        $topLevelId = $path[$rootIndex + 1];
        try {
            $topLevel = $this->categoryRepository->get($topLevelId, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this->cache[$pid] = null;
        }

        if (!$topLevel->getIsActive()) {
            return $this->cache[$pid] = null;
        }

        $sub = ((int) $chosen->getId() !== (int) $topLevel->getId()) ? $chosen : null;

        return $this->cache[$pid] = ['top' => $topLevel, 'sub' => $sub];
    }
}
