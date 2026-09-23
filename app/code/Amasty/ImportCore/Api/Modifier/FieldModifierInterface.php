<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Modifier;

use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Api\Config\Profile\ModifierInterface;

interface FieldModifierInterface
{
    /**
     * Transforms value and returns modified result
     *
     * @param mixed $value
     * @return mixed
     */
    public function transform($value);

    /**
     * @param FieldInterface $field
     * @param $requestData
     * @return array
     */
    public function prepareArguments(FieldInterface $field, $requestData): array;

    /**
     * @return array
     */
    public function getJsConfig(): array;

    /**
     * @param ModifierInterface $modifier
     * @return array
     */
    public function getValue(ModifierInterface $modifier): array;

    /**
     * Get modifier group
     *
     * @return string
     */
    public function getGroup(): string;
}
