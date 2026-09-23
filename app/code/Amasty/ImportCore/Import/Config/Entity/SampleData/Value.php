<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Entity\SampleData;

use Amasty\ImportCore\Api\Config\Entity\SampleData\ValueInterface;

class Value implements ValueInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    /**
     * @inheritDoc
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @inheritDoc
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
