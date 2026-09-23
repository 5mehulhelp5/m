<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Entity\Field;

use Amasty\ImportCore\Api\Config\Entity\Field\Configuration\IdentificationInterface;
use Magento\Framework\DataObject;

class Identification extends DataObject implements IdentificationInterface
{
    public const IDENTIFIER = 'identifier';
    public const LABEL = 'label';

    public function setIsIdentifier(bool $isIdentifier): void
    {
        $this->setData(self::IDENTIFIER, $isIdentifier);
    }

    public function isIdentifier(): bool
    {
        return (bool)$this->getData(self::IDENTIFIER);
    }

    public function setLabel(string $label): void
    {
        $this->setData(self::LABEL, $label);
    }

    public function getLabel(): string
    {
        return $this->getData(self::LABEL);
    }
}
