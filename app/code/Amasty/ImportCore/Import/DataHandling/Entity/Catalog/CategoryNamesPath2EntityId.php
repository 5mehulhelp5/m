<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\Entity\Catalog;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class CategoryNamesPath2EntityId
{
    /**
     * Category path delimiter
     */
    public const PATH_DELIMITER = '/';

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var array
     */
    private $namesPathToEntityIdMap = [];

    /**
     * @var int
     */
    private $processedCategories = 0;

    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Converts category names path to entity Id
     *
     * @param string $namesPath
     * @return int|null
     */
    public function execute(string $namesPath): ?int
    {
        $namesPath = $this->standardizeString($namesPath);
        $pathToEntityIdMap = $this->getNamesPathToEntityIdMap();

        return $pathToEntityIdMap[$namesPath] ?? null;
    }

    /**
     * Get names path to entity Id map
     *
     * @return array
     * @throws LocalizedException
     */
    private function getNamesPathToEntityIdMap(): array
    {
        /** @var Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path')
            ->setStoreId(Store::DEFAULT_STORE_ID);

        if (!$this->namesPathToEntityIdMap || $this->processedCategories != $collection->getSize()) {
            /** @var Category $category */
            foreach ($collection as $category) {
                $structure = explode(self::PATH_DELIMITER, $category->getPath());
                $pathSize = count($structure);

                if ($pathSize > 1) {
                    $path = [];
                    for ($i = 1; $i < $pathSize; $i++) {
                        /** @var Category $item */
                        $item = $collection->getItemById((int)$structure[$i]);
                        if (!$item) {
                            continue 2;
                        }

                        $name = $item->getName();
                        $path[] = $this->quotePathDelimiter($name);
                    }

                    $namesPath = $this->standardizeString(implode(self::PATH_DELIMITER, $path));
                    $this->namesPathToEntityIdMap[$namesPath] = (int)$category->getId();
                }
            }

            $this->processedCategories = $collection->getSize();
        }

        return $this->namesPathToEntityIdMap;
    }

    /**
     * Quoting path delimiter in string
     *
     * @param string $string
     * @return string
     */
    private function quotePathDelimiter(string $string): string
    {
        return str_replace(self::PATH_DELIMITER, '\\' . self::PATH_DELIMITER, $string);
    }

    /**
     * Standardize a string
     *
     * @param string $string
     * @return string
     */
    private function standardizeString(string $string): string
    {
        return mb_strtolower($string);
    }
}
