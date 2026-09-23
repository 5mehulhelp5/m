<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\EmptyToNull;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\EmptyToNull
 */
class EmptyToNullTest extends TestCase
{
    /**
     * @var EmptyToNull
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new EmptyToNull([]);
    }

    /**
     * @param $value
     * @param $expectedResult
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
            'empty' => ['', null],
            'not_empty' => ['test', 'test'],
            'space' => [' ', null],
            'null' => [null, null],
            'numerical' => [24, 24]
        ];
    }
}
