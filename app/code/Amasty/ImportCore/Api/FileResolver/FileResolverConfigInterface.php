<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\FileResolver;

interface FileResolverConfigInterface
{
    /**
     * Get file resolver config using file resolver type Id
     *
     * @param string $type
     * @return array
     */
    public function get(string $type): array;

    /**
     * Get all file resolvers configs
     *
     * @return array
     */
    public function all(): array;
}
