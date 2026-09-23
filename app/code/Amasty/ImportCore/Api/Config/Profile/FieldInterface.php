<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Profile;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Profile field
 */
interface FieldInterface extends ExtensibleDataInterface
{
    public const FIELD_TYPE = 'field';

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldInterface
     */
    public function setName(string $name): FieldInterface;

    /**
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * @param string $label
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldInterface
     */
    public function setLabel(?string $label): FieldInterface;

    /**
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * @param string|null $value
     * @return FieldInterface
     */
    public function setValue(?string $value): FieldInterface;

    /**
     * @return string|null
     */
    public function getMap(): ?string;

    /**
     * @param string $map
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldInterface
     */
    public function setMap(string $map): FieldInterface;

    /**
     * @return \Amasty\ImportCore\Api\Config\Profile\ModifierInterface[]
     */
    public function getModifiers(): array;

    /**
     * @param \Amasty\ImportCore\Api\Config\Profile\ModifierInterface[] $modifiers
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldInterface
     */
    public function setModifiers(?array $modifiers): FieldInterface;

    /**
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldExtensionInterface
     */
    public function getExtensionAttributes(): FieldExtensionInterface;

    /**
     * @param \Amasty\ImportCore\Api\Config\Profile\FieldExtensionInterface $extensionAttributes
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldInterface
     */
    public function setExtensionAttributes(
        FieldExtensionInterface $extensionAttributes
    ): FieldInterface;
}
