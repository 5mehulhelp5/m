<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Csv;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    public const SETTING_MAX_LINE_LENGTH = 0;
    public const SETTING_FIELD_DELIMITER = ',';
    public const SETTING_FIELD_ENCLOSURE_CHARACTER = '"';
    public const SETTING_COMBINE_CHILD_ROWS = false;
    public const SETTING_CHILD_ROW_SEPARATOR = ',';
    public const SETTING_PREFIX = '.';

    public const SEPARATOR = 'separator';
    public const ENCLOSURE = 'enclosure';
    public const COMBINE_CHILD_ROWS = 'combine_child_rows';
    public const CHILD_ROW_SEPARATOR = 'child_row_separator';
    public const MAX_LINE_LENGTH = 'max_line_length';
    public const PREFIX = 'prefix';

    public function getSeparator(): ?string
    {
        return $this->getData(self::SEPARATOR) ?? self::SETTING_FIELD_DELIMITER;
    }

    public function setSeparator(?string $separator): ConfigInterface
    {
        $this->setData(self::SEPARATOR, $separator);

        return $this;
    }

    public function getEnclosure(): ?string
    {
        return $this->getData(self::ENCLOSURE) ?? self::SETTING_FIELD_ENCLOSURE_CHARACTER;
    }

    public function setEnclosure(?string $enclosure): ConfigInterface
    {
        $this->setData(self::ENCLOSURE, $enclosure);

        return $this;
    }

    public function getMaxLineLength(): ?int
    {
        return $this->getData(self::MAX_LINE_LENGTH) ?? self::SETTING_MAX_LINE_LENGTH;
    }

    public function setMaxLineLength(?int $maxLineLength): ConfigInterface
    {
        $this->setData(self::MAX_LINE_LENGTH, $maxLineLength);

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->getData(self::PREFIX) ?? self::SETTING_PREFIX;
    }

    public function setPrefix(?string $prefix): ConfigInterface
    {
        $this->setData(self::PREFIX, $prefix);

        return $this;
    }

    public function isCombineChildRows(): ?bool
    {
        return $this->getData(self::COMBINE_CHILD_ROWS) ?? self::SETTING_COMBINE_CHILD_ROWS;
    }

    public function setCombineChildRows(?bool $combineChildRows): ConfigInterface
    {
        $this->setData(self::COMBINE_CHILD_ROWS, $combineChildRows);

        return $this;
    }

    public function getChildRowSeparator(): ?string
    {
        return $this->getData(self::CHILD_ROW_SEPARATOR) ?? self::SETTING_CHILD_ROW_SEPARATOR;
    }

    public function setChildRowSeparator(?string $childRowSeparator): ConfigInterface
    {
        $this->setData(self::CHILD_ROW_SEPARATOR, $childRowSeparator);

        return $this;
    }
}
