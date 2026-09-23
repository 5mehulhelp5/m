<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Config\OptionSource;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Import\Config\DemoEntitiesPool;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Config\OptionSource\Entities;
use Amasty\ImportCore\Import\Utils\Hash;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Config\OptionSource\Entities
 */
class EntitiesTest extends TestCase
{
    /**
     * @var Entities
     */
    private $entities;

    /**
     * @var EntityConfigProvider|MockObject
     */
    private $entityConfigProviderMock;

    /**
     * @var Hash|MockObject
     */
    private $hashMock;

    /**
     * @var DemoEntitiesPool|MockObject
     */
    private $demoEntitiesPoolMock;

    public function setUp(): void
    {
        $this->entityConfigProviderMock = $this->createMock(EntityConfigProvider::class);
        $this->hashMock = $this->createMock(Hash::class);
        $this->demoEntitiesPoolMock = $this->createMock(DemoEntitiesPool::class);
        $this->entities = new Entities(
            $this->entityConfigProviderMock,
            $this->hashMock,
            $this->demoEntitiesPoolMock
        );
    }

    /**
     * Data provider for toOptionArray
     * @return array
     */
    public function toOptionArrayDataProvider(): array
    {
        return [
            'skipHiddenInLists' => [
                true,
                'Entity Group',
                'Entity Name',
                'entity_code',
                'b7df81cd7133084c2f06b3da941a9e7d',
                []
            ],
            'notHiddenInLists' => [
                false,
                'Entity Group',
                'Entity Name',
                'entity_code',
                'b7df81cd7133084c2f06b3da941a9e7d',
                [
                    0 => [
                        'label' => 'Entity Group',
                        'optgroup' => [
                            0 => [
                                'label' => 'Entity Name',
                                'value' => 'entity_code',
                            ]
                        ],
                        'value' => 'b7df81cd7133084c2f06b3da941a9e7d'
                    ]
                ]
            ]
        ];
    }

    /**
     * @param bool $isHiddenInLists
     * @param string $group
     * @param string $name
     * @param string $entityCode
     * @param string $hash
     * @param array $expectedResult
     * @dataProvider toOptionArrayDataProvider
     */
    public function testToOptionArray(
        bool $isHiddenInLists,
        string $group,
        string $name,
        string $entityCode,
        string $hash,
        array $expectedResult
    ) {
        /** @var EntityConfigInterface|\MockObject $entityConfigMock */
        $entityConfigMock = $this->createConfiguredMock(
            EntityConfigInterface::class,
            [
                'isHiddenInLists' => $isHiddenInLists,
                'getGroup' => $group,
                'getName' => $name,
                'getEntityCode' => $entityCode
            ]
        );
        $this->entityConfigProviderMock->expects($this->once())
            ->method('getConfig')
            ->willReturn([$entityConfigMock]);
        $this->hashMock->expects($this->any())
            ->method('hash')
            ->willReturnMap([
                [$group, $hash]
            ]);
        $result = $this->entities->toOptionArray();

        $this->assertEquals($expectedResult, $result);
    }
}
