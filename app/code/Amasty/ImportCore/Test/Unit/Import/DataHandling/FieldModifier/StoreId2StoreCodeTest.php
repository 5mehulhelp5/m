<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\StoreId2StoreCode;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\StoreId2StoreCode
 */
class StoreId2StoreCodeTest extends TestCase
{
    /**
     * @var StoreId2StoreCode
     */
    private $modifier;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->modifier = new StoreId2StoreCode([], $this->storeManagerMock);
    }

    /**
     * @param $value
     * @param array $map
     * @param $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, array $map, $expectedResult)
    {
        $reflection = new \ReflectionClass(StoreId2StoreCode::class);

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

        $reflection = new \ReflectionClass(StoreId2StoreCode::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);

        $this->modifier->transform('default');
        $expected = [
            1 => 'default',
            2 => 'custom'
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
                1,
                [1 => 'default'],
                'default'
            ],
            'array' => [
                [1, 2],
                [1 => 'default', 2 => 'custom'],
                ['default', 'custom']
            ],
            'all' => [
                0,
                [1 => 'default'],
                'all'
            ]
        ];
    }
}
