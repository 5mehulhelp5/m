<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Map as MapModifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Map
 */
class MapTest extends TestCase
{
    /**
     * @var MapModifier
     */
    private $modifier;

    protected function setUp(): void
    {
        $this->modifier = new MapModifier([]);
    }

    /**
     * @param string $value
     * @param array $config
     * @param string $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform(string $value, array $config, string $expectedResult)
    {
        $reflection = new \ReflectionClass(MapModifier::class);

        $configProperty = $reflection->getProperty('config');
        $configProperty->setAccessible(true);
        $configProperty->setValue($this->modifier, $config);

        $this->assertSame($expectedResult, $this->modifier->transform($value));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            'empty' => [
                'test',
                [MapModifier::IS_MULTIPLE => false],
                'test'
            ],
            'simple_test_match' => [
                'simple',
                [
                    MapModifier::IS_MULTIPLE => false,
                    MapModifier::MAP => [
                        'simple' => 'Simple Product',
                        'configurable' => 'Configurable Product',
                    ]
                ],
                'Simple Product'
            ],
            'simple_test_mismatch' => [
                'bundle',
                [
                    MapModifier::IS_MULTIPLE => false,
                    MapModifier::MAP => [
                        'simple' => 'Simple Product',
                        'configurable' => 'Configurable Product',
                    ]
                ],
                'bundle'
            ],
            'simple_test_multiple' => [
                'simple,configurable',
                [
                    MapModifier::IS_MULTIPLE => true,
                    MapModifier::MAP => [
                        'simple' => 'Simple Product',
                        'configurable' => 'Configurable Product',
                    ]
                ],
                'Simple Product,Configurable Product'
            ]
        ];
    }
}
