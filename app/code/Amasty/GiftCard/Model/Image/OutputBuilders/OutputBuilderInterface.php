<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\OutputBuilders;

use Amasty\GiftCard\Api\Data\ImageElementsInterface;

interface OutputBuilderInterface
{
    /**
     * @param ImageElementsInterface[] $imageElements
     * @return string
     */
    public function build(array $imageElements): string;
}
