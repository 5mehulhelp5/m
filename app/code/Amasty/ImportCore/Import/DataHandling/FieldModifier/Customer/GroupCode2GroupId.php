<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier\Customer;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Customer\Attribute\Source;

class GroupCode2GroupId extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var Source\Group
     */
    private $groupSource;

    /**
     * @var array|null
     */
    private $map = null;

    public function __construct($config, Source\Group $groupSource)
    {
        parent::__construct($config);
        $this->groupSource = $groupSource;
    }

    public function transform($value)
    {
        if (!is_array($value)) {
            $map = $this->getMap();

            return $map[$value] ?? $value;
        }

        return $value;
    }

    private function getMap(): array
    {
        if ($this->map === null) {
            $map = [];
            foreach ($this->groupSource->getAllOptions() as $groupOption) {
                $map[$groupOption['label']] = $groupOption['value'];
            }

            $map['ALL GROUPS'] = (string)GroupInterface::CUST_GROUP_ALL;
            $map['NOT LOGGED IN'] = (string)GroupInterface::NOT_LOGGED_IN_ID;
            $this->map = $map;
        }

        return $this->map;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Convert group_code to group_id')->getText();
    }
}
