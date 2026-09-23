<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Source;

interface SourceConfigInterface
{
    /**
     * Get source config using source type Id
     *
     * @param string $type
     * @return array
     */
    public function get(string $type): array;

    /**
     * Get all sources configs
     *
     * @return array
     */
    public function all(): array;
}
