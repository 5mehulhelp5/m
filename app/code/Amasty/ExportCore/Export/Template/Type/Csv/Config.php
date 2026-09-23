<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Template\Type\Csv;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    public const SETTING_MAX_LINE_LENGTH = 0;
    public const SETTING_FIELD_DELIMITER = ',';
    public const SETTING_FIELD_ENCLOSURE_CHARACTER = '"';
    public const SETTING_HAS_HEADER_ROW = true;
    public const SETTING_COMBINE_CHILD_ROWS = false;
    public const SETTING_CHILD_ROW_SEPARATOR = ',';
    public const SETTING_POSTFIX = '.';

    public const SEPARATOR = 'separator';
    public const ENCLOSURE = 'enclosure';
    public const HAS_HEADER_ROW = 'has_header_row';
    public const COMBINE_CHILD_ROWS = 'combine_child_rows';
    public const DUPLICATE_PARENT_DATA = 'duplicate_parent_data';
    public const CHILD_ROW_SEPARATOR = 'child_row_separator';
    public const MAX_LINE_LENGTH = 'max_line_length';
    public const POSTFIX = 'postfix';
    public const EXPORT_WITHOUT_ENTITY_KEYS = 'export_without_entity_keys';

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

    public function isHasHeaderRow(): ?bool
    {
        return $this->getData(self::HAS_HEADER_ROW) ?? self::SETTING_HAS_HEADER_ROW;
    }

    public function setHasHeaderRow(?bool $hasHeaderRow): ConfigInterface
    {
        $this->setData(self::HAS_HEADER_ROW, $hasHeaderRow);

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

    public function getPostfix(): ?string
    {
        return $this->getData(self::POSTFIX) ?? self::SETTING_POSTFIX;
    }

    public function setPostfix(?string $postfix): ConfigInterface
    {
        $this->setData(self::POSTFIX, $postfix);

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

    public function isDuplicateParentData(): ?bool
    {
        return $this->getData(self::DUPLICATE_PARENT_DATA);
    }

    public function setDuplicateParentData(?bool $duplicate): ConfigInterface
    {
        $this->setData(self::DUPLICATE_PARENT_DATA, $duplicate);

        return $this;
    }

    public function isExportWithoutEntityKeys(): ?bool
    {
        return $this->hasData(self::EXPORT_WITHOUT_ENTITY_KEYS)
            ? (bool)$this->_getData(self::EXPORT_WITHOUT_ENTITY_KEYS)
            : null;
    }

    public function setIsExportWithoutEntityKeys(?bool $isExportWithoutKeys): ConfigInterface
    {
        $this->setData(self::EXPORT_WITHOUT_ENTITY_KEYS, $isExportWithoutKeys);

        return $this;
    }
}
