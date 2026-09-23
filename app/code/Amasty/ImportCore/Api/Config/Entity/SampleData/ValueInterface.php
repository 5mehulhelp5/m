<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity\SampleData;

interface ValueInterface
{
    /**
     * @return string
     */
    public function getField();

    /**
     * @param string $field
     *
     * @return void
     */
    public function setField($field);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setValue($value);
}
