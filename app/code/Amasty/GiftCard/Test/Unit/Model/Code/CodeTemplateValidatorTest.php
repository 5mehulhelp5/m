<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Test\Unit\Model\Code;

use Amasty\GiftCard\Model\Code\CodeTemplateValidator;
use Amasty\GiftCard\Model\Code\Repository;
use PHPUnit\Framework\TestCase;

class CodeTemplateValidatorTest extends TestCase
{
    /**
     * @dataProvider generateCodesWrongTemplateDataProvider
     */
    public function testGenerateCodesWithWrongTemplate($template, $qty, $exceptionMessage)
    {
        $codeRepositoryMock = $this->createMock(Repository::class);
        $this->expectExceptionMessage($exceptionMessage);

        $validator = new CodeTemplateValidator($codeRepositoryMock);
        $validator->validateTemplate($template, $qty);
    }

    /**
     * @return array
     */
    public function generateCodesWrongTemplateDataProvider()
    {
        return [
            [//first assertion - invalid template name
             'TEST',
             1,
             'Please add {L} or {D} placeholders into the template "TEST"'
            ],
            [
                'TEST_{D}',
                10,
                'Maximum number of code combinations for the current template is 8,
            please update Quantity field accordingly'
            ]
        ];
    }
}
