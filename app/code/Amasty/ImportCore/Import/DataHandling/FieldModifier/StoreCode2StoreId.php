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

class StoreCode2StoreId extends AbstractModifier implements FieldModifierInterface
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
            foreach ($value as &$storeCode) {
                $storeCode = $map[$storeCode] ?? null;
            }

            return $value;
        }

        return $map[$value] ?? null;
    }

    private function getMap(): array
    {
        if ($this->map === null) {
            $this->map = [];
            foreach ($this->storeManager->getStores(true) as $store) {
                $this->map[$store->getCode()] = $store->getId();
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
        return __('Store Code to Store Id')->getText();
    }
}
