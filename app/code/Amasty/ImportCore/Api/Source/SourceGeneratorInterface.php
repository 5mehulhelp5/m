<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Source;

use Amasty\ImportCore\Import\Config\ProfileConfig;

interface SourceGeneratorInterface
{
    /**
     * Generate file content
     *
     * @param ProfileConfig $profileConfig
     * @param array $data
     * @return string
     */
    public function generate(ProfileConfig $profileConfig, array $data): string;

    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension(): string;
}
