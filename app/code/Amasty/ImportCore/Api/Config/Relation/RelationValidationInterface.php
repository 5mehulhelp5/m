<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Relation;

/**
 * Relation validation config
 */
interface RelationValidationInterface
{
    /**
     * @return \Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface
     */
    public function getConfigClass();

    /**
     * @param \Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface $configClass
     *
     * @return void
     */
    public function setConfigClass($configClass);

    /**
     * @return string[]
     */
    public function getExcludeBehaviors();

    /**
     * @param string[] $behaviorCodes
     *
     * @return void
     */
    public function setExcludeBehaviors($behaviorCodes);

    /**
     * @return string[]
     */
    public function getIncludeBehaviors();

    /**
     * @param string[] $behaviorCodes
     *
     * @return void
     */
    public function setIncludeBehaviors($behaviorCodes);
}
