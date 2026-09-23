<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Uppercase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Uppercase
 */
class UppercaseTest extends TestCase
{
    /**
     * @var Uppercase
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new Uppercase([]);
    }

    /**
     * @param string $value
     * @param string $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform(string $value, string $expectedResult)
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
            'simple_test' => ['test', 'TEST'],
            'mixed' => ['hElLo', 'HELLO'],
            'unicode' => ['ТьмАФФки', 'ТЬМАФФКИ'],
        ];
    }
}
