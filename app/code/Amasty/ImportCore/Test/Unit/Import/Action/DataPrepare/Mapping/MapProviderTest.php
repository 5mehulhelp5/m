<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\DataPrepare\Mapping;

use Amasty\ImportCore\Api\Source\SourceDataStructureInterface;
use Amasty\ImportCore\Import\Action\DataPrepare\Mapping\MapProvider;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\DataPrepare\Mapping\MapProvider
 */
class MapProviderTest extends TestCase
{
    /**
     * @var MapProvider
     */
    private $mapProvider;

    protected function setUp(): void
    {
        $this->mapProvider = new MapProvider($this->createMock(EntityConfigProvider::class));
    }

    /**
     * @param SourceDataStructureInterface|MockObject $dataStructureMock
     * @param array $expectedResult
     * @dataProvider getSubEntitiesMapDataProvider
     */
    public function testGetSubEntitiesMap($dataStructureMock, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->mapProvider->getSubEntitiesMap($dataStructureMock)
        );
    }

    /**
     * @return array
     */
    public function getSubEntitiesMapDataProvider()
    {
        return [
            [
                $this->createConfiguredMock(
                    SourceDataStructureInterface::class,
                    ['getSubEntityStructures' => []]
                ),
                []
            ],
            [
                $this->createConfiguredMock(
                    SourceDataStructureInterface::class,
                    [
                        'getSubEntityStructures' => [
                            $this->createConfiguredMock(
                                SourceDataStructureInterface::class,
                                [
                                    'getMap' => 'sub_entity',
                                    'getEntityCode' => 'sub_entity_code'
                                ]
                            )
                        ]
                    ]
                ),
                ['sub_entity' => 'sub_entity_code']
            ]
        ];
    }
}
