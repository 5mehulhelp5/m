<?php

declare(strict_types=1);

namespace CyberSolutionsLLC\CategoryTiles\Block\Widget;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;

class CategoryTiles extends Template implements BlockInterface
{
    protected $_template = 'CyberSolutionsLLC_CategoryTiles::widget/category_tiles.phtml';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Magento Category Factory
     * @var CategoryFactory
     */
    protected CategoryFactory $categoryFactory;

    /**
     * @param Template\Context $context
     * @param StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    public function getTiles(): array
    {
        $tiles = [];
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        for ($i = 1; $i <= 3; $i++) {
            $imageUrl = $this->getData("tile{$i}_image");
            $text = $this->getData("tile{$i}_name");
            $catId = $this->getData("tile{$i}_category_id");

            if ($imageUrl && $text && $catId) {
                $catId = (int)str_replace('category/', '', $catId);

                $category = $this->categoryFactory->create();
                $category->load($catId);
                $tiles[] = [
                    'image_url' => $mediaUrl . $imageUrl,
                    'text' => $text,
                    'url' => $category->getUrl(),
                ];
            }
        }

        return $tiles;
    }

    public function getHeading(): string
    {
        return (string) $this->getData('heading') ?: 'Shop by Platform';
    }
}
