<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Template\Type\Xml;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    public const HEADER = 'header';
    public const ITEM = 'item';
    public const FOOTER = 'footer';
    public const XSL_TEMPLATE = 'xsl_template';

    public function getHeader(): ?string
    {
        return $this->getData(self::HEADER);
    }

    public function setHeader(?string $header): ConfigInterface
    {
        $this->setData(self::HEADER, $header);

        return $this;
    }

    public function getItem(): ?string
    {
        return $this->getData(self::ITEM);
    }

    public function setItem(?string $item): ConfigInterface
    {
        $this->setData(self::ITEM, $item);

        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->getData(self::FOOTER);
    }

    public function setFooter(?string $footer): ConfigInterface
    {
        $this->setData(self::FOOTER, $footer);

        return $this;
    }

    public function getXslTemplate(): ?string
    {
        return $this->getData(self::XSL_TEMPLATE);
    }

    public function setXslTemplate(?string $xslTemplate): ConfigInterface
    {
        $this->setData(self::XSL_TEMPLATE, $xslTemplate);

        return $this;
    }
}
