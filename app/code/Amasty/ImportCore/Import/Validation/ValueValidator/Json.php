<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Validation\ValueValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Json implements FieldValidatorInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            try {
                return (bool)$this->serializer->unserialize($row[$field]);
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }
}
