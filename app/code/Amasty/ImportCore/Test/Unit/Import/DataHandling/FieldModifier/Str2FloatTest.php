<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Str2Float;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Str2Float
 */
class Str2FloatTest extends TestCase
{
    /**
     * @var Str2Float
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new Str2Float([]);
    }

    /**
     * @param string|null $value
     * @param float|string|null $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->modifier->transform($value));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            ['0', 0.0],
            ['2.64', 2.64],
            ['-2.64', -2.64],
            ['', ''],
            [null, null],
            ['string', 'string']
        ];
    }
}
