<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image;

class ImageElementConfigProvider
{
    /**
     * @var ImageElementConfig[]
     */
    private $configs = [];

    public function __construct(
        array $elementsConfigs = []
    ) {
        $this->initializeElementsConfigs($elementsConfigs);
    }

    /**
     * @return ImageElementConfig[]
     */
    public function getAll(): array
    {
        return $this->configs;
    }

    public function get(string $code = ''): ?ImageElementConfig
    {
        return $this->configs[$code] ?? null;
    }

    /**
     * @param ImageElementConfig[] $configs
     */
    private function initializeElementsConfigs(array $configs)
    {
        foreach ($configs as $config) {
            if (!$config instanceof ImageElementConfig) {
                throw new \LogicException(
                    sprintf('Generator config must implement %s', ImageElementConfig::class)
                );
            }
            $this->configs[$config->getCode()] = $config;
        }

        uasort($this->configs, function ($first, $second) {
            return $first->getSortOrder() <=> $second->getSortOrder();
        });
    }
}
