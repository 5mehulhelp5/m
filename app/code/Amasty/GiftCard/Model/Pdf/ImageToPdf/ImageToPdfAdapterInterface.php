<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Pdf\ImageToPdf;

interface ImageToPdfAdapterInterface
{
    /**
     * @param string $imageHtml
     * @param array|null $size
     * @return string
     */
    public function render(string $imageHtml, ?array $size = null): string;
}
