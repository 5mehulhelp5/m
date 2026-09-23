<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Config\Eav\Attribute;

use Amasty\ImportCore\Import\Config\Eav\Attribute\OptionsConverter;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface;
use Amasty\ImportExportCore\Config\Xml\ArgumentsPrepare;
use Magento\Eav\Api\Data\AttributeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Config\Eav\Attribute\OptionsConverter
 */
class OptionsConverterTest extends TestCase
{
    /**
     * @var OptionsConverter
     */
    private $optionsConverter;

    /**
     * @var ArgumentsPrepare|MockObject
     */
    private $argumentsPreparerMock;

    public function setUp(): void
    {
        $this->argumentsPreparerMock = $this->createMock(ArgumentsPrepare::class);
        $this->optionsConverter = new OptionsConverter($this->argumentsPreparerMock);
    }

    /**
     * Data provider for prepareForConvert
     * @return array
     */
    public function prepareForConvertDataProvider(): array
    {
        return [
            'emptyValue' => [
                [
                    [
                        'label' => 'Test Option',
                        'value' => ''
                    ]
                ],
                'someArgument',
                [
                    'name' => 'someArgument',
                    'xsi:type' => 'array',
                    'item' => []
                ]
            ],
            'valueNotArray' => [
                [
                    [
                        'label' => 'Test Option',
                        'value' => 1
                    ]
                ],
                'someArgument',
                [
                    'name' => 'someArgument',
                    'xsi:type' => 'array',
                    'item' => [
                        0 => [
                            'name' => 0,
                            'xsi:type' => 'array',
                            'item' => [
                                0 => [
                                    'name' => 'value',
                                    'xsi:type' => 'number',
                                    'value' => 1,
                                ],
                                1 => [
                                    'name' => 'label',
                                    'xsi:type' => 'string',
                                    'value' => 'Test Option',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'valueIsArray' => [
                [
                    [
                        'label' => 'Test Option',
                        'value' => [
                            [
                                'label' => 'Some Option',
                                'value' => 'testValue'
                            ]
                        ]
                    ]
                ],
                'testArgument',
                [
                    'name' => 'testArgument',
                    'xsi:type' => 'array',
                    'item' => [
                        0 => [
                            'name' => 0,
                            'xsi:type' => 'array',
                            'item' => [
                                0 => [
                                    'name' => 0,
                                    'xsi:type' => 'array',
                                    'item' => [
                                        0 => [
                                            'name' => 'value',
                                            'xsi:type' => 'string',
                                            'value' => 'testValue',
                                        ],
                                        1 => [
                                            'name' => 'label',
                                            'xsi:type' => 'string',
                                            'value' => 'Some Option',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function testToConfigArgumentsEmptyOptions()
    {
        $this->argumentsPreparerMock->expects($this->never())
            ->method('execute');
        $this->assertEquals([], $this->optionsConverter->toConfigArguments([], 'someArgument'));
    }

    /**
     * @param array $options
     * @param string|int $argumentName
     * @param array $expectedResult
     * @dataProvider prepareForConvertDataProvider
     */
    public function testPrepareForConvert(array $options, $argumentName, array $expectedResult)
    {
        $reflection = new \ReflectionClass(OptionsConverter::class);
        $method = $reflection->getMethod('prepareForConvert');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->optionsConverter, [$options, $argumentName]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetConfigArgumentDataType()
    {
        $frontendInput = 'select';
        $attributeMock = $this->createMock(AttributeInterface::class);
        $attributeMock->expects($this->once())
            ->method('getFrontendInput')
            ->willReturn($frontendInput);
        $argumentMock = $this->createMock(ArgumentInterface::class);

        $argumentData = [
            'name' => 'dataType',
            'xsi:type' => 'string',
            'value' => 'select'
        ];
        $this->argumentsPreparerMock->expects($this->once())
            ->method('execute')
            ->willReturnMap([
                [[$argumentData], [$argumentMock]]
            ]);
        $this->assertSame(
            [$argumentMock],
            $this->optionsConverter->getConfigArgumentDataType($attributeMock)
        );
    }
}
