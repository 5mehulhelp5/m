<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Test\Unit\Config\ConfigClass;

use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface;
use Amasty\ImportExportCore\Config\ConfigClass\Factory;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportExportCore\Config\ConfigClass\Factory
 */
class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    /**
     * @var InterpreterInterface|MockObject
     */
    private $argumentInterpreterMock;

    protected function setUp(): void
    {
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->argumentInterpreterMock = $this->createMock(InterpreterInterface::class);

        $this->factory = new Factory(
            $this->objectManagerMock,
            $this->argumentInterpreterMock
        );
    }

    public function testCreateObject()
    {
        $className = DummyClass::class;
        $configClassMock = $this->createMock(ConfigClassInterface::class);

        $classMock = $this->createMock($className);

        $argumentName = 'tableName';
        $argumentType = 'string';
        $argumentValue = 'catalog_product_entity';
        $argumentMock = $this->createMock(ArgumentInterface::class);

        $configClassMock->expects($this->once())
            ->method('getName')
            ->willReturn($className);
        $configClassMock->expects($this->once())
            ->method('getArguments')
            ->willReturn([$argumentMock]);

        $argumentMock->expects($this->once())
            ->method('getName')
            ->willReturn($argumentName);
        $argumentMock->expects($this->once())
            ->method('getType')
            ->willReturn($argumentType);
        $argumentMock->expects($this->once())
            ->method('getValue')
            ->willReturn($argumentValue);

        $this->argumentInterpreterMock->expects($this->once())
            ->method('evaluate')
            ->with(
                [
                    'name' => $argumentName,
                    'xsi:type' => $argumentType,
                    'value' => $argumentValue
                ]
            )
            ->willReturn($argumentValue);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(
                $className,
                ['config' => [$argumentName => $argumentValue]]
            )
            ->willReturn($classMock);

        $this->assertSame($classMock, $this->factory->createObject($configClassMock));
    }

    /**
     * @param ArgumentInterface[]|MockObject[] $arguments
     * @param array $expectedResult
     * @dataProvider convertArgumentsDataProvider
     */
    public function testConvertArguments(array $arguments, array $expectedResult)
    {
        $reflection = new \ReflectionClass(Factory::class);
        $method = $reflection->getMethod('convertArguments');
        $method->setAccessible(true);

        $this->assertEquals(
            $expectedResult,
            $method->invokeArgs($this->factory, [$arguments])
        );
    }

    public function convertArgumentsDataProvider()
    {
        return [
            [
                [
                    $this->createConfiguredMock(
                        ArgumentInterface::class,
                        [
                            'getName' => 'someStringArg',
                            'getType' => 'string',
                            'getValue' => 'someStringValue'
                        ]
                    )
                ],
                [
                    'someStringArg' => [
                        'name' => 'someStringArg',
                        'xsi:type' => 'string',
                        'value' => 'someStringValue'
                    ]
                ]
            ],
            [
                [
                    $this->createConfiguredMock(
                        ArgumentInterface::class,
                        [
                            'getName' => 'someArrayArg',
                            'getType' => 'array',
                            'getItems' => [
                                $this->createConfiguredMock(
                                    ArgumentInterface::class,
                                    [
                                        'getName' => 'someStringArg1',
                                        'getType' => 'string',
                                        'getValue' => 'someString1Value'
                                    ]
                                ),
                                $this->createConfiguredMock(
                                    ArgumentInterface::class,
                                    [
                                        'getName' => 'someStringArg2',
                                        'getType' => 'string',
                                        'getValue' => 'someString2Value'
                                    ]
                                )
                            ]
                        ]
                    )
                ],
                [
                    'someArrayArg' => [
                        'name' => 'someArrayArg',
                        'xsi:type' => 'array',
                        'item' => [
                            'someStringArg1' => [
                                'name' => 'someStringArg1',
                                'xsi:type' => 'string',
                                'value' => 'someString1Value'
                            ],
                            'someStringArg2' => [
                                'name' => 'someStringArg2',
                                'xsi:type' => 'string',
                                'value' => 'someString2Value'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
