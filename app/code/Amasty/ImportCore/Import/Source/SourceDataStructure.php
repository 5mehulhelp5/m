<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source;

use Amasty\ImportCore\Api\Source\SourceDataStructureInterface;
use Magento\Framework\DataObject;

class SourceDataStructure extends DataObject implements SourceDataStructureInterface
{
    public const ENTITY_CODE = 'entity_code';
    public const ID_FIELD_NAME = 'id_field_name';
    public const PARENT_ID_FIELD_NAME = 'parent_id_field_name';
    public const MAP = 'map';
    public const IS_ALLOW_EMPTY = 'is_allow_empty';
    public const FIELDS = 'fields';
    public const FIELDS_MAP = 'fields_map';
    public const SUB_ENTITY_STRUCTURES = 'sub_entity_structures';

    /**
     * Data key for sub entities data
     */
    public const SUB_ENTITIES_DATA_KEY = '__sub_entities';

    public function getEntityCode()
    {
        return $this->getData(self::ENTITY_CODE);
    }

    public function getMap()
    {
        return $this->getData(self::MAP);
    }

    public function isAllowEmpty()
    {
        return (bool)$this->getData(self::IS_ALLOW_EMPTY);
    }

    public function getFields()
    {
        return $this->getData(self::FIELDS) ?? [];
    }

    public function getFieldName($field)
    {
        $fieldsMap = $this->getData(self::FIELDS_MAP);

        return array_search($field, $fieldsMap);
    }

    public function getIdFieldName()
    {
        return $this->getData(self::ID_FIELD_NAME);
    }

    public function getParentIdFieldName()
    {
        return $this->getData(self::PARENT_ID_FIELD_NAME);
    }

    public function getSubEntityStructures()
    {
        return $this->getData(self::SUB_ENTITY_STRUCTURES) ?? [];
    }
}
