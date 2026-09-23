<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Config\CustomEntity;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class CustomResourceModel extends AbstractDb
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $idField;

    public function __construct(
        $tableName,
        $idField,
        Context $context,
        $connectionName = null
    ) {
        $this->tableName = $tableName;
        $this->idField = $idField;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init($this->tableName, $this->idField);
    }
}
