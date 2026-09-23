<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Xml;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    public const ITEM_PATH = 'item_path';
    public const XSL_TEMPLATE = 'xsl_template';

    public function getItemPath()
    {
        return $this->getData(self::ITEM_PATH);
    }

    public function setItemPath($itemPath)
    {
        $this->setData(self::ITEM_PATH, $itemPath);
    }

    public function getXslTemplate()
    {
        return $this->getData(self::XSL_TEMPLATE);
    }

    public function setXslTemplate($xslTemplate)
    {
        $this->setData(self::XSL_TEMPLATE, $xslTemplate);
    }
}
