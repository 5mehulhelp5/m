<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\DefaultValue;
use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\DefaultValue
 */
class DefaultValueTest extends TestCase
{
    public const DEFAULT_VALUE = 'default_val';

    /**
     * @var DefaultValue
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new DefaultValue(
            ['value' => self::DEFAULT_VALUE],
            $this->createMock(ArgumentConverter::class)
        );
    }

    /**
     * @param mixed $value
     * @param bool $force
     * @param mixed $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, $force, $expectedResult)
    {
        $reflection = new \ReflectionClass(DefaultValue::class);

        $forceProperty = $reflection->getProperty('force');
        $forceProperty->setAccessible(true);
        $forceProperty->setValue($this->modifier, $force);

        $this->assertEquals($expectedResult, $this->modifier->transform($value));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            ['', false, self::DEFAULT_VALUE],
            ['', true, self::DEFAULT_VALUE],
            [null, false, self::DEFAULT_VALUE],
            [null, true, self::DEFAULT_VALUE],
            ['Some string', false, 'Some string'],
            ['Some string', true, self::DEFAULT_VALUE],
            [5, false, 5],
            [5, true, self::DEFAULT_VALUE]
        ];
    }
}
