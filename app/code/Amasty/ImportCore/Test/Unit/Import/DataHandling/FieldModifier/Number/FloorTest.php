<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier\Number;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Number\Floor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Number\Floor
 */
class FloorTest extends TestCase
{
    /**
     * @var Floor
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new Floor([]);
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
            [4.3, 4],
            [-3.14, -4]
        ];
    }
}
