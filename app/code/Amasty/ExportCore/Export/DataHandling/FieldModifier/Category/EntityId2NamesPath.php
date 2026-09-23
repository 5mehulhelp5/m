<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\DataHandling\FieldModifier\Category;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\FieldModifier\Catalog\CategoriesPathResolver;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Store\Model\Store;

class EntityId2NamesPath extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var CategoriesPathResolver
     */
    private $categoriesPathResolver;

    /**
     * Current row storeId
     *
     * @var int
     */
    private $storeId;

    public function __construct(
        CategoriesPathResolver $categoriesPathResolver,
        array $config = []
    ) {
        parent::__construct($config);
        $this->categoriesPathResolver = $categoriesPathResolver;
    }

    public function prepareRowOptions(array $row)
    {
        $this->storeId = isset($row['store_id']) ? (int)$row['store_id'] : Store::DEFAULT_STORE_ID;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!empty($value)) {
            $path = $this->categoriesPathResolver->getNamePathByEntityId((int)$value, $this->storeId);
            if ($path) {
                return $path;
            }
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Category Entity Id To Names Path')->render();
    }
}
