<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\Template;

interface TemplateConfigInterface
{
    public function get(string $type): array;

    public function all(): array;
}
