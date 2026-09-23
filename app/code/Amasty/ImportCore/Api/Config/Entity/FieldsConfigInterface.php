<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity;

/**
 * Entity fields config
 */
interface FieldsConfigInterface
{
    /**
     * @return \Amasty\ImportCore\Api\Config\Entity\Field\FieldInterface[]
     */
    public function getFields();

    /**
     * @param \Amasty\ImportCore\Api\Config\Entity\Field\FieldInterface[] $fields
     *
     * @return void
     */
    public function setFields($fields);

    /**
     * @return string
     */
    public function getRowActionClass();

    /**
     * @param string $class
     *
     * @return void
     */
    public function setRowActionClass($class);

    /**
     * @return \Amasty\ImportCore\Api\Config\Entity\Row\ValidationInterface
     */
    public function getRowValidation();

    /**
     * @param \Amasty\ImportCore\Api\Config\Entity\Row\ValidationInterface $class
     *
     * @return void
     */
    public function setRowValidation($class);

    /**
     * @return \Amasty\ImportCore\Api\Config\Entity\SampleData\RowInterface[]
     */
    public function getSampleData();

    /**
     * @param \Amasty\ImportCore\Api\Config\Entity\SampleData\RowInterface[] $sampleData
     *
     * @return void
     */
    public function setSampleData($sampleData);

    /**
     * @return \Amasty\ImportCore\Api\Config\Entity\FieldsConfigExtensionInterface
     */
    public function getExtensionAttributes(): FieldsConfigExtensionInterface;

    /**
     * @param \Amasty\ImportCore\Api\Config\Entity\FieldsConfigExtensionInterface $extensionAttributes
     *
     * @return void
     */
    public function setExtensionAttributes(
        FieldsConfigExtensionInterface $extensionAttributes
    );
}
