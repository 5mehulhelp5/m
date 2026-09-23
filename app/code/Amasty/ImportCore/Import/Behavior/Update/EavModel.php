<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Behavior\Update;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\BehaviorInterface;
use Amasty\ImportCore\Import\Behavior\EavModel as EavModelBehavior;

class EavModel extends EavModelBehavior implements BehaviorInterface
{
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $idFieldName = $this->getIdFieldName();
        foreach ($this->prepareAttributeValues($data) as $row) {
            $model = $this->loadForScope((int)$row[$idFieldName], $this->getScopeValue($row));
            if ($model) {
                $model->setData($row);
                $this->save($model);
            }
        }

        return $this->resultFactory->create();
    }
}
