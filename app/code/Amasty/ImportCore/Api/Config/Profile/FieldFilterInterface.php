<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Profile;

use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Profile field filter
 */
interface FieldFilterInterface extends ExtensibleDataInterface
{
    /**
     * @return string|null
     */
    public function getField(): ?string;

    /**
     * @param string|null $field
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setField(?string $field): FieldFilterInterface;

    /**
     * @return string|null
     */
    public function getCondition(): ?string;

    /**
     * @param string|null $condition
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setCondition(?string $condition): FieldFilterInterface;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $filterType
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setType(?string $filterType): FieldFilterInterface;

    /**
     * @return \Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface|null
     */
    public function getFilterClass(): ?ConfigClassInterface;

    /**
     * @param \Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface|null $filterClass
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setFilterClass(?ConfigClassInterface $filterClass): FieldFilterInterface;

    /**
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldFilterExtensionInterface
     */
    public function getExtensionAttributes(): FieldFilterExtensionInterface;

    /**
     * @param \Amasty\ImportCore\Api\Config\Profile\FieldFilterExtensionInterface $extensionAttributes
     *
     * @return \Amasty\ImportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setExtensionAttributes(
        FieldFilterExtensionInterface $extensionAttributes
    ): FieldFilterInterface;
}
