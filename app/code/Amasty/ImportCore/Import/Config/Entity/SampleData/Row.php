<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Entity\SampleData;

use Amasty\ImportCore\Api\Config\Entity\SampleData\RowInterface;
use Amasty\ImportCore\Api\Config\Entity\SampleData\ValueInterface;

class Row implements RowInterface
{
    /**
     * @var ValueInterface[]
     */
    private $values;

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     */
    public function setValues($values)
    {
        $this->values = $values;
    }
}
