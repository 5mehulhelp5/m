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
use Amasty\ImportCore\Import\Behavior\EavEntityManager as EavEntityManagerBehavior;
use Magento\Framework\EntityManager\Operation\AttributeInterface;

class EavEntityManager extends EavEntityManagerBehavior implements BehaviorInterface
{
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        /** @var BehaviorResultInterface $result */
        $result = $this->resultFactory->create();

        /** @var AttributeInterface[] $actions */
        $actions = $this->attributePool->getActions($this->entityType, 'update');
        foreach ($this->prepareAttributeValues($data) as $row) {
            $this->applyActions($actions, $row);
        }

        return $result;
    }
}
