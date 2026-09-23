<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardExtension\Quote;

/**
 * Class used to add additional toggle settings in GiftCard modules config
 */
class AllowedTotalModifiersPool
{
    /**
     * @var array
     */
    private $modifiersConfig;

    /**
     * @param array $modifiersConfig ['module_name' => ['id', 'label']]
     */
    public function __construct(
        array $modifiersConfig = []
    ) {
        $this->initConfig($modifiersConfig);
    }

    public function get(string $module): ?array
    {
        return $this->modifiersConfig[$module] ?? null;
    }

    public function getAll(): array
    {
        return $this->modifiersConfig;
    }

    /**
     * @throws \LogicException
     */
    private function initConfig(array $modifiersConfig): void
    {
        foreach ($modifiersConfig as $moduleConfig) {
            foreach ($moduleConfig as $fieldConfig) {
                if (!isset($fieldConfig['id'], $fieldConfig['label'])) {
                    throw new \LogicException(
                        '"id" or "label" is missing in allowed total modifiers pool configuration.'
                    );
                }
            }
        }
        $this->modifiersConfig = $modifiersConfig;
    }
}
