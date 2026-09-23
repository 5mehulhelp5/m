<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\PostProcessing;

interface PostProcessingConfigInterface
{
    public function get(string $type): array;
    public function all(): array;
}
