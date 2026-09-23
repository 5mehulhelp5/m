<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\DataPrepare\Source;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\Source\SourceDataStructureInterface;
use Amasty\ImportCore\Import\Action\DataPrepare\Source\SourceDataProcessor;
use Amasty\ImportCore\Import\Source\Data\DataStructureProvider;
use Amasty\ImportCore\Import\Source\SourceDataStructure;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\DataPrepare\Source\SourceDataProcessor
 */
class SourceDataProcessorTest extends TestCase
{
    /**
     * @var SourceDataProcessor
     */
    private $sourceDataProcessor;

    /**
     * @var DataStructureProvider|MockObject
     */
    private $dataStructureProviderMock;

    protected function setUp(): void
    {
        $this->dataStructureProviderMock = $this->createMock(DataStructureProvider::class);
        $this->sourceDataProcessor = new SourceDataProcessor($this->dataStructureProviderMock);
    }

    /**
     * @param array $row
     * @param SourceDataStructureInterface|MockObject $dataStructureMock
     * @param array $expectedResult
     * @dataProvider convertToImportProcessStructureDataProvider
     */
    public function testConvertToImportProcessStructure(
        $row,
        $dataStructureMock,
        $expectedResult
    ) {
        /** @var ImportProcessInterface|MockObject $importProcessMock */
        $importProcessMock = $this->createMock(ImportProcessInterface::class);
        $entityConfigMock = $this->createMock(EntityConfigInterface::class);
        $profileConfigMock = $this->createMock(ProfileConfigInterface::class);

        $importProcessMock->expects($this->once())
            ->method('getEntityConfig')
            ->willReturn($entityConfigMock);
        $importProcessMock->expects($this->once())
            ->method('getProfileConfig')
            ->willReturn($profileConfigMock);
        $this->dataStructureProviderMock->expects($this->once())
            ->method('getDataStructure')
            ->with($entityConfigMock, $profileConfigMock)
            ->willReturn($dataStructureMock);

        $this->assertEquals(
            $expectedResult,
            $this->sourceDataProcessor->convertToImportProcessStructure($importProcessMock, $row)
        );
    }

    /**
     * @return array
     */
    public function convertToImportProcessStructureDataProvider()
    {
        return [
            [
                ['field' => 'value'],
                $this->createConfiguredMock(
                    SourceDataStructureInterface::class,
                    ['getSubEntityStructures' => []]
                ),
                ['field' => 'value']
            ],
            [
                [
                    'field' => 'value',
                    'sub_entity' => [
                        ['sub_entity_field' => 'sub_entity_value']
                    ]
                ],
                $this->createConfiguredMock(
                    SourceDataStructureInterface::class,
                    [
                        'getSubEntityStructures' => [
                            $this->createConfiguredMock(
                                SourceDataStructureInterface::class,
                                [
                                    'getMap' => 'sub_entity',
                                    'getSubEntityStructures' => []
                                ]
                            )
                        ]
                    ]
                ),
                [
                    'field' => 'value',
                    SourceDataStructure::SUB_ENTITIES_DATA_KEY => [
                        'sub_entity' => [
                            ['sub_entity_field' => 'sub_entity_value']
                        ]
                    ]
                ]
            ],
            [
                [
                    'field' => 'value',
                    'sub_entity' => [
                        ['sub_entity_field' => null],
                        ['sub_entity_field' => 'sub_entity_value']
                    ]
                ],
                $this->createConfiguredMock(
                    SourceDataStructureInterface::class,
                    [
                        'getSubEntityStructures' => [
                            $this->createConfiguredMock(
                                SourceDataStructureInterface::class,
                                [
                                    'getMap' => 'sub_entity',
                                    'getSubEntityStructures' => []
                                ]
                            )
                        ]
                    ]
                ),
                [
                    'field' => 'value',
                    SourceDataStructure::SUB_ENTITIES_DATA_KEY => [
                        'sub_entity' => [
                            ['sub_entity_field' => 'sub_entity_value']
                        ]
                    ]
                ]
            ]
        ];
    }
}
