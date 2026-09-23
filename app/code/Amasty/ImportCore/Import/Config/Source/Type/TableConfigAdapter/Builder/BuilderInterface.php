<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Source\Type\TableConfigAdapter\Builder;

use Amasty\ImportCore\Import\Config\ProfileConfig;

interface BuilderInterface
{
    /**
     * @param ProfileConfig $profileConfig
     * @param array $data
     * @return array
     */
    public function build(ProfileConfig $profileConfig, array $data): array;
}
