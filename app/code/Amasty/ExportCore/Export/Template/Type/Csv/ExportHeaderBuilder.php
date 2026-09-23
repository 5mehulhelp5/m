<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Template\Type\Csv;

use Amasty\ExportCore\Api\ExportProcessInterface;

class ExportHeaderBuilder
{
    public function getHeader(
        array $row,
        string $prefix = '',
        string $postfix = '',
        ?ExportProcessInterface $exportProcess = null
    ): array {
        return $this->processHeaderRows($row, $prefix, $postfix);
    }

    private function processHeaderRows(
        array $row,
        string $prefix = '',
        string $postfix = ''
    ): array {
        $header = [];
        foreach ($row as $key => $value) {
            if (is_array($value)) {
                //phpcs:ignore
                $header = array_merge($header, $this->processHeaderRows($value, $key, $postfix));
            } else {
                $header[] = (!empty($prefix) ? $prefix . $postfix : '') . $key;
            }
        }

        return $header;
    }
}
