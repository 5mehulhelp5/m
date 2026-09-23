<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Validation;

use Amasty\ImportCore\Api\ImportProcessInterface;

interface ValidationProviderInterface
{
    /**
     * Get field validators registry
     *
     * @param ImportProcessInterface $importProcess
     * @param FieldValidatorInterface[][] $validatorsForCollect
     * @return FieldValidatorInterface[][]
     */
    public function getFieldValidators(
        ImportProcessInterface $importProcess,
        array &$validatorsForCollect = []
    ): array;

    /**
     * Get row validators registry
     *
     * @param ImportProcessInterface $importProcess
     * @param RowValidatorInterface[][] $validatorsForCollect
     * @return RowValidatorInterface[][]
     */
    public function getRowValidators(
        ImportProcessInterface $importProcess,
        array &$validatorsForCollect = []
    ): array;

    /**
     * Get relation validators registry
     *
     * @param ImportProcessInterface $importProcess
     * @param RelationValidatorInterface[][] $validatorsForCollect
     * @return RelationValidatorInterface[][]
     */
    public function getRelationValidators(
        ImportProcessInterface $importProcess,
        array &$validatorsForCollect = []
    ): array;
}
