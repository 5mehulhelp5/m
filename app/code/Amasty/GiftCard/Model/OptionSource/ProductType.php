<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ProductType implements OptionSourceInterface
{

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    private $type;

    public function __construct(
        \Magento\Catalog\Model\Product\Type $type
    ) {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->type->getAllOptions();
    }
}
