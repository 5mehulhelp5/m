<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use LogicException;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;

class OptionValueValidator implements FieldValidatorInterface
{
    public const OPTION_SOURCE = 'optionSource';

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $validationResult = [];

    /**
     * @var OptionSourceInterface
     */
    private $optionSourceObject;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $config = []
    ) {
        $this->config = $config;
        if (empty($this->config[self::OPTION_SOURCE])) {
            throw new LogicException('Option source is not specified for OptionValueValidator.');
        }
        $optionSource = $this->config[self::OPTION_SOURCE];
        $this->optionSourceObject = $objectManager->get($optionSource);
        if (!$this->optionSourceObject instanceof OptionSourceInterface) {
            throw new LogicException('optionSource for OptionValueValidator must implement OptionSourceInterface');
        }
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $optionValue = trim((string)$row[$field]);
            if (!isset($this->validationResult[$optionValue])) {
                $this->validationResult[$optionValue] = $this->isValidOptionValue($optionValue);
            }

            return $this->validationResult[$optionValue];
        }

        return true;
    }

    private function isValidOptionValue($optionValue): bool
    {
        foreach ($this->optionSourceObject->toOptionArray() as $item) {
            if (isset($item['value']) && $item['value'] == $optionValue) {
                return true;
            }
        }

        return false;
    }
}
