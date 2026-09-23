<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\WebsiteCode2WebsiteId;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\WebsiteCode2WebsiteId
 */
class WebsiteCode2WebsiteIdTest extends TestCase
{
    /**
     * @var WebsiteCode2WebsiteId
     */
    private $modifier;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->modifier = new WebsiteCode2WebsiteId([], $this->storeManagerMock);
    }

    /**
     * @param $value
     * @param array $map
     * @param $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, array $map, $expectedResult)
    {
        $reflection = new \ReflectionClass(WebsiteCode2WebsiteId::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);
        $mapProperty->setValue($this->modifier, $map);

        $this->assertSame($expectedResult, $this->modifier->transform($value));
    }

    public function testGetMap()
    {
        $websiteMock = $this->createMock(WebsiteInterface::class);
        $websiteMock->expects($this->any())->method('getId')
            ->willReturnOnConsecutiveCalls(1, 2);
        $websiteMock->expects($this->any())->method('getCode')
            ->willReturnOnConsecutiveCalls('base', 'custom');
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn([$websiteMock, $websiteMock]);

        $reflection = new \ReflectionClass(WebsiteCode2WebsiteId::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);

        $this->modifier->transform('base');
        $expected = [
            'All Websites' => 0,
            'base' => 1,
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
                'base',
                ['base' => 1],
                1
            ],
            'undefined' => [
                'custom',
                ['default' => 1],
                'custom'
            ]
        ];
    }
}
