<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Config\ConfigClass;

interface ArgumentInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType($type);

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

    /**
     * @return \Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface[]
     */
    public function getItems();

    /**
     * @param \Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface[] $items
     *
     * @return void
     */
    public function setItems($items);
}
