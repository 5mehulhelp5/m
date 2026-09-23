<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\CapitalizeEachWord;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\CapitalizeEachWord
 */
class CapitalizeEachWordTest extends TestCase
{
    /**
     * @var CapitalizeEachWord
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new CapitalizeEachWord([]);
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
            ['some text', 'Some Text'],
            ['Some text', 'Some Text']
        ];
    }
}
