<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Xml;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getItemPath();

    /**
     * @param string $itemPath
     *
     * @return void
     */
    public function setItemPath($itemPath);

    /**
     * @return string
     */
    public function getXslTemplate();

    /**
     * @param string $xslTemplate
     *
     * @return void
     */
    public function setXslTemplate($xslTemplate);
}
