<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Magento\Store\Model\StoreManagerInterface;

class StoreId2StoreCode extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array|null
     */
    private $map = null;

    public function __construct(
        $config,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($config);
        $this->storeManager = $storeManager;
    }

    public function transform($value)
    {
        $map = $this->getMap();

        if (is_array($value)) {
            foreach ($value as &$storeId) {
                $storeId = $map[$storeId] ?? 'all';
            }

            return $value;
        }

        return $map[$value] ?? 'all';
    }

    private function getMap(): array
    {
        if ($this->map === null) {
            $this->map = [];
            foreach ($this->storeManager->getStores(true) as $store) {
                $this->map[$store->getId()] = $store->getCode();
            }
        }

        return $this->map;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Store Is to Store Code')->getText();
    }
}
