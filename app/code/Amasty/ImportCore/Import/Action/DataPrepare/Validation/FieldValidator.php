<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Action\DataPrepare\Validation;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;

class FieldValidator implements FieldValidatorInterface
{
    /**
     * @var FieldValidatorInterface
     */
    private $validator;

    /**
     * @var string
     */
    private $errorMessage;

    public function __construct(
        FieldValidatorInterface $validator,
        string $errorMessage
    ) {
        $this->validator = $validator;
        $this->errorMessage = $errorMessage;
    }

    public function validate(array $row, string $field): bool
    {
        return $this->validator->validate($row, $field);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
