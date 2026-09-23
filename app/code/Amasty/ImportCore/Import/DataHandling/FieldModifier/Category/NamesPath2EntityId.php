<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier\Category;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\Entity\Catalog\CategoryNamesPath2EntityId;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;

class NamesPath2EntityId extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var NamesPath2EntityId
     */
    private $catNamesPath2EntityId;

    public function __construct($config, CategoryNamesPath2EntityId $catNamesPath2EntityId)
    {
        parent::__construct($config);
        $this->catNamesPath2EntityId = $catNamesPath2EntityId;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!empty($value)) {
            return $this->catNamesPath2EntityId->execute((string)$value);
        }

        return $value;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Category Names Path To Entity Id')->getText();
    }
}
