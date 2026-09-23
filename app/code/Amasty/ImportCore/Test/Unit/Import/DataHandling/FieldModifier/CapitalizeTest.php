<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Capitalize;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Capitalize
 */
class CapitalizeTest extends TestCase
{
    /**
     * @var Capitalize
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new Capitalize([]);
    }

    /**
     * @param mixed $value
     * @param mixed $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->modifier->transform($value));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            ['', ''],
            [5, 5],
            ['some text', 'Some text'],
            ['Some text', 'Some text']
        ];
    }
}
