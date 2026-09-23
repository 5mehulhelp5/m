<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity\SampleData;

interface RowInterface
{
    /**
     * @return \Amasty\ImportCore\Api\Config\Entity\SampleData\ValueInterface[]
     */
    public function getValues();

    /**
     * @param \Amasty\ImportCore\Api\Config\Entity\SampleData\ValueInterface[] $values
     *
     * @return void
     */
    public function setValues($values);
}
