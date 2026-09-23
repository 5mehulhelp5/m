<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\Config\Profile;

use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface;

interface FieldFilterInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return string|null
     */
    public function getField(): ?string;

    /**
     * @param string|null $field
     *
     * @return \Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setField(?string $field): FieldFilterInterface;

    /**
     * @return string|null
     */
    public function getCondition(): ?string;

    /**
     * @param string|null $condition
     *
     * @return \Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setCondition(?string $condition): FieldFilterInterface;

    /**
     * @return bool|null
     */
    public function getApplyAfterModifier(): ?bool;

    /**
     * @param bool|null $apply
     *
     * @return FieldFilterInterface
     */
    public function setApplyAfterModifier(?bool $apply): FieldFilterInterface;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $filterType
     *
     * @return \Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setType(?string $filterType): FieldFilterInterface;

    /**
     * @return \Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface|null
     */
    public function getFilterClass(): ?ConfigClassInterface;

    /**
     * @param \Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface|null $filterType
     *
     * @return \Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setFilterClass(?ConfigClassInterface $filterClass): FieldFilterInterface;

    /**
     * @return \Amasty\ExportCore\Api\Config\Profile\FieldFilterExtensionInterface
     */
    public function getExtensionAttributes(): \Amasty\ExportCore\Api\Config\Profile\FieldFilterExtensionInterface;

    /**
     * @param \Amasty\ExportCore\Api\Config\Profile\FieldFilterExtensionInterface $extensionAttributes
     *
     * @return \Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface
     */
    public function setExtensionAttributes(
        \Amasty\ExportCore\Api\Config\Profile\FieldFilterExtensionInterface $extensionAttributes
    ): FieldFilterInterface;
}
