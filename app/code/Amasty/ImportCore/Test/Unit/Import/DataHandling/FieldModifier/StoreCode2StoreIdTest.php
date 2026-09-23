<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\StoreCode2StoreId;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\StoreCode2StoreId
 */
class StoreCode2StoreIdTest extends TestCase
{
    /**
     * @var StoreCode2StoreId
     */
    private $modifier;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->modifier = new StoreCode2StoreId([], $this->storeManagerMock);
    }

    /**
     * @param $value
     * @param array $map
     * @param $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, array $map, $expectedResult)
    {
        $reflection = new \ReflectionClass(StoreCode2StoreId::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);
        $mapProperty->setValue($this->modifier, $map);

        $this->assertSame($expectedResult, $this->modifier->transform($value));
    }

    public function testGetMap()
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())->method('getId')
            ->willReturnOnConsecutiveCalls(1, 2);
        $storeMock->expects($this->any())->method('getCode')
            ->willReturnOnConsecutiveCalls('default', 'custom');
        $this->storeManagerMock->expects($this->once())
            ->method('getStores')
            ->willReturn([$storeMock, $storeMock]);

        $reflection = new \ReflectionClass(StoreCode2StoreId::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);

        $this->modifier->transform('default');
        $expected = [
            'default' => 1,
            'custom' => 2
        ];

        $this->assertEquals($expected, $mapProperty->getValue($this->modifier));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            'basic' => [
                'default',
                ['default' => 1],
                1
            ],
            'undefined' => [
                'custom',
                ['default' => 1],
                null
            ],
            'array' => [
                ['default', 'custom'],
                ['default' => 1, 'custom' => 2],
                [1, 2]
            ],
            'all' => [
                'all',
                ['default' => 1],
                null
            ]
        ];
    }
}
