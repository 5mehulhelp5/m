<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Source\Type;

use Magento\Framework\DataObject;

class TableConfigAdapter extends DataObject
{
    public const ITEM_TAG = 'item_tag';
    public const IS_DUPLICATE_PARENT_DATA = 'is_duplicate_parent_data';
    public const IS_COMBINE_CHILD_ROWS = 'is_combine_child_rows';
    public const CHILD_ROW_SEPARATOR = 'child_row_separator';

    public function getItemTag(): ?string
    {
        return $this->getData(self::ITEM_TAG);
    }

    public function getIsDuplicateParentData(): bool
    {
        return (bool)$this->getData(self::IS_DUPLICATE_PARENT_DATA);
    }

    public function getIsCombineChildRows(): ?bool
    {
        return $this->getData(self::IS_COMBINE_CHILD_ROWS);
    }

    public function getChildRowSeparator(): ?string
    {
        return $this->getData(self::CHILD_ROW_SEPARATOR);
    }
}
