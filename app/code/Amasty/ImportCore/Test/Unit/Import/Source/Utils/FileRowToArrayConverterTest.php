<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Source\Utils;

use Amasty\ImportCore\Import\Source\Utils\FileRowToArrayConverter;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class FileRowToArrayConverterTest extends TestCase
{
    public const TEST_STRUCTURE = [
        'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
        'base_grand_total' => '',
        'sales_order_item' => [
            'item_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
            'order_id' => FileRowToArrayConverter::PARENT_ID_KEY,
            'sku' => ''
        ],
        'sales_shipment' => [
            'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
            'order_id' => FileRowToArrayConverter::PARENT_ID_KEY,
            'total_qty' => '',
            'sales_shipment_item' => [
                'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
                'parent_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                'sku' => ''
            ]
        ]
    ];

    /**
     * @var FileRowToArrayConverter
     */
    private $converter;

    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->converter = $objectManager->getObject(
            FileRowToArrayConverter::class,
            [
                'arrayManager' => $objectManager->getObject(ArrayManager::class)
            ]
        );
    }

    public function testConvertRowToHeaderStructure(): void
    {
        $rowData = ['1', '10', '1', '1', 'test', '1', '1', '1', '1', '1', 'test'];
        $expected = [
            'entity_id' => '1',
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'item_id' => '1',
                    'order_id' => '1',
                    'sku' => 'test'
                ]
            ],
            'sales_shipment' => [
                [
                    'entity_id' => '1',
                    'order_id' => '1',
                    'total_qty' => '1',
                    'sales_shipment_item' => [
                        [
                            'entity_id' => '1',
                            'parent_id' => '1',
                            'sku' => 'test'
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEquals(
            $expected,
            $this->converter->convertRowToHeaderStructure(self::TEST_STRUCTURE, $rowData)
        );
    }

    /**
     * @dataProvider formatMergedSubEntitiesDataProvider
     */
    public function testFormatMergedSubEntities(array $rowData, array $structure, array $expected): void
    {
        $result = $this->converter->formatMergedSubEntities($rowData, $structure, ',');

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider mergeRowsDataProvider
     */
    public function testMergeRows(array $firstRow, array $secondRow, array $structure, array $expected): void
    {
        $result = $this->converter->mergeRows($firstRow, $secondRow, $structure);

        $this->assertEquals($expected, $result);
    }

    private function formatMergedSubEntitiesDataProvider(): array
    {
        $rowWithPk = [
            'entity_id' => '1',
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'item_id' => '1',
                    'order_id' => '1',
                    'sku' => 'test'
                ]
            ],
            'sales_shipment' => [
                [
                    'entity_id' => '1',
                    'order_id' => '1',
                    'total_qty' => '1',
                    'sales_shipment_item' => [
                        [
                            'entity_id' => '1',
                            'parent_id' => '1',
                            'sku' => 'test'
                        ]
                    ]
                ]
            ]
        ];
        $rowWithoutPk = [
            'entity_id' => '1',
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'order_id' => '1',
                    'sku' => 'test'
                ]
            ],
            'sales_shipment' => [
                [
                    'order_id' => '1',
                    'total_qty' => '1',
                    'sales_shipment_item' => [
                        [
                            'parent_id' => '1',
                            'sku' => 'test'
                        ]
                    ]
                ]
            ]
        ];
        $rowMergedWithPk = [
            'entity_id' => '1',
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'item_id' => '1,2',
                    'order_id' => '1,1',
                    'sku' => 'test,test2'
                ]
            ],
            'sales_shipment' => [
                [
                    'entity_id' => '1,2',
                    'order_id' => '1,1',
                    'total_qty' => '1,2',
                    'sales_shipment_item' => [
                        [
                            'entity_id' => '1,2,3,4',
                            'parent_id' => '1,1,2,2',
                            'sku' => 'test,test2,test,test2'
                        ]
                    ]
                ]
            ]
        ];
        $rowMergedWithoutPk = [
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'order_id' => '1,1',
                    'sku' => 'test,test2'
                ]
            ],
            'sales_shipment' => [
                [
                    'order_id' => '1,1',
                    'total_qty' => '1,2',
                    'sales_shipment_item' => [
                        [
                            'parent_id' => '1,1,2,2',
                            'sku' => 'test,test2,test,test2'
                        ]
                    ]
                ]
            ]
        ];
        $expectedMergedRowWithPk = [
            'entity_id' => '1',
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'item_id' => '1',
                    'order_id' => '1',
                    'sku' => 'test'
                ],
                [
                    'item_id' => '2',
                    'order_id' => '1',
                    'sku' => 'test2'
                ]
            ],
            'sales_shipment' => [
                [
                    'entity_id' => '1',
                    'order_id' => '1',
                    'total_qty' => '1',
                    'sales_shipment_item' => [
                        [
                            'entity_id' => '1',
                            'parent_id' => '1',
                            'sku' => 'test'
                        ],
                        [
                            'entity_id' => '2',
                            'parent_id' => '1',
                            'sku' => 'test2'
                        ]
                    ]
                ],
                [
                    'entity_id' => '2',
                    'order_id' => '1',
                    'total_qty' => '2',
                    'sales_shipment_item' => [
                        [
                            'entity_id' => '3',
                            'parent_id' => '2',
                            'sku' => 'test'
                        ],
                        [
                            'entity_id' => '4',
                            'parent_id' => '2',
                            'sku' => 'test2'
                        ]
                    ]
                ]
            ]
        ];
        $expectedMergedRowWithoutPk = [
            'base_grand_total' => '10',
            'sales_order_item' => [
                [
                    'order_id' => '1',
                    'sku' => 'test'
                ],
                [
                    'order_id' => '1',
                    'sku' => 'test2'
                ]
            ],
            'sales_shipment' => [
                [
                    'order_id' => '1',
                    'total_qty' => '1',
                    'sales_shipment_item' => [
                        [
                            'parent_id' => '1',
                            'sku' => 'test'
                        ],
                        [
                            'parent_id' => '1',
                            'sku' => 'test2'
                        ]
                    ]
                ],
                [
                    'order_id' => '1',
                    'total_qty' => '2',
                    'sales_shipment_item' => [
                        [
                            'parent_id' => '2',
                            'sku' => 'test'
                        ],
                        [
                            'parent_id' => '2',
                            'sku' => 'test2'
                        ]
                    ]
                ]
            ]
        ];
        $structureWithPk = [
            'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
            'base_grand_total' => '',
            'sales_order_item' => [
                'item_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
                'order_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                'sku' => ''
            ],
            'sales_shipment' => [
                'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
                'order_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                'total_qty' => '',
                'sales_shipment_item' => [
                    'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
                    'parent_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                    'sku' => ''
                ]
            ]
        ];
        $structureWithoutPk = [
            'entity_id' => FileRowToArrayConverter::ENTITY_ID_KEY,
            'base_grand_total' => '',
            'sales_order_item' => [
                'order_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                'sku' => ''
            ],
            'sales_shipment' => [
                'order_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                'total_qty' => '',
                'sales_shipment_item' => [
                    'parent_id' => FileRowToArrayConverter::PARENT_ID_KEY,
                    'sku' => ''
                ]
            ]
        ];

        return [
            'without merged subentites' => [
                $rowWithPk,
                $structureWithPk,
                $rowWithPk
            ],
            'without merged subentities no PK' => [
                $rowWithoutPk,
                $structureWithoutPk,
                $rowWithoutPk
            ],
            'with merged subentites' => [
                $rowMergedWithPk,
                $structureWithPk,
                $expectedMergedRowWithPk
            ],
            'with merged subentites no PK' => [
                $rowMergedWithoutPk,
                $structureWithoutPk,
                $expectedMergedRowWithoutPk
            ]
        ];
    }

    private function mergeRowsDataProvider(): array
    {
        return [
            'merge rows with nesting = 3' => [
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'field' => '1'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '',
                    'subentity2' => [
                        [
                            'field' => '',
                            'subentity3' => [
                                [
                                    'field' => '2'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '1',
                    'subentity2' => [
                        'field' => '1,2',
                        'subentity3' => [
                            'field' => '1,2'
                        ]
                    ]
                ],
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'field' => '1'
                                ],
                                [
                                    'field' => '2'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'merge rows with nesting = 4' => [
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'id_field' => '1',
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'id_field' => '1',
                                            'field' => '1'
                                        ]
                                    ]
                                ],
                                [
                                    'id_field' => '2',
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'id_field' => '2',
                                            'field' => '2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '',
                    'subentity2' => [
                        [
                            'field' => '',
                            'subentity3' => [
                                [
                                    'id_field' => '',
                                    'field' => '',
                                    'subentity4' => [
                                        [
                                            'id_field' => '3',
                                            'field' => '2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '1',
                    'subentity2' => [
                        'field' => '1,2',
                        'subentity3' => [
                            'id_field' => '1',
                            'field' => '2',
                            'subentity4' => [
                                'id_field' => '1',
                                'field' => '2'
                            ]
                        ]
                    ]
                ],
                'merge rows with nesting = 3' => [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'id_field' => '1',
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'id_field' => '1',
                                            'field' => '1'
                                        ]
                                    ]
                                ],
                                [
                                    'id_field' => '2',
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'id_field' => '2',
                                            'field' => '2'
                                        ],
                                        [
                                            'id_field' => '3',
                                            'field' => '2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'merge rows without subentities ID Field' => [
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'field' => '1'
                                        ]
                                    ]
                                ],
                                [
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'field' => '2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '',
                    'subentity2' => [
                        [
                            'field' => '',
                            'subentity3' => [
                                [
                                    'field' => '',
                                    'subentity4' => [
                                        [
                                            'field' => '2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '1',
                    'subentity2' => [
                        'field' => '2',
                        'subentity3' => [
                            'field' => '2',
                            'subentity4' => [
                                'field' => '2'
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'field' => '1'
                                        ]
                                    ]
                                ],
                                [
                                    'field' => '1',
                                    'subentity4' => [
                                        [
                                            'field' => '2'
                                        ],
                                        [
                                            'field' => '2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'no pk main field' => [
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'field' => '1'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '',
                    'subentity2' => [
                        [
                            'field' => '',
                            'subentity3' => [
                                [
                                    'field' => '2'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'field' => '',
                    'subentity2' => [
                        'field' => '',
                        'subentity3' => [
                            'field' => ''
                        ]
                    ]
                ],
                [
                    'field' => '1',
                    'subentity2' => [
                        [
                            'field' => '1',
                            'subentity3' => [
                                [
                                    'field' => '1'
                                ],
                                [
                                    'field' => '2'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
