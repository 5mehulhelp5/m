<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier\Customer;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Customer\GroupCode2GroupId;
use Magento\Customer\Model\Customer\Attribute\Source\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Customer\GroupCode2GroupId
 */
class GroupCode2GroupIdTest extends TestCase
{
    /**
     * @var GroupCode2GroupId
     */
    private $modifier;

    /**
     * @var Group|MockObject
     */
    private $groupMock;

    protected function setUp(): void
    {
        $this->groupMock = $this->createMock(Group::class);
        $this->modifier = new GroupCode2GroupId([], $this->groupMock);
    }

    /**
     * @param $value
     * @param array $map
     * @param $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, array $map, $expectedResult)
    {
        $reflection = new \ReflectionClass(GroupCode2GroupId::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);
        $mapProperty->setValue($this->modifier, $map);

        $this->assertSame($expectedResult, $this->modifier->transform($value));
    }

    public function testGetMap()
    {
        $this->groupMock->expects($this->once())
            ->method('getAllOptions')
            ->willReturn([
                [
                    'value' => 1,
                    'label' => 'General'
                ],
                [
                    'value' => 2,
                    'label' => 'Wholesale'
                ],
                [
                    'value' => 3,
                    'label' => 'Retailer'
                ]
            ]);

        $reflection = new \ReflectionClass(GroupCode2GroupId::class);

        $mapProperty = $reflection->getProperty('map');
        $mapProperty->setAccessible(true);

        $this->modifier->transform('General');
        $expected = [
            'General' => 1,
            'Wholesale' => 2,
            'Retailer' => 3,
            'ALL GROUPS' => '32000',
            'NOT LOGGED IN' => '0',
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
                'General',
                ['General' => 1],
                1
            ],
            'undefined' => [
                'Custom',
                ['General' => 1],
                'Custom'
            ],
            'array' => [
                ['General', 'Retailer'],
                ['General' => 1, 'Retailer' => 2],
                ['General', 'Retailer']
            ]
        ];
    }
}
