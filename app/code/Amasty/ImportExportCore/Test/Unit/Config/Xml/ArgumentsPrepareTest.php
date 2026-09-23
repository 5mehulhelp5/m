<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Test\Unit\Config\ConfigClass;

use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterfaceFactory;
use Amasty\ImportExportCore\Config\Xml\ArgumentsPrepare;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportExportCore\Config\Xml\ArgumentsPrepare
 */
class ArgumentsPrepareTest extends TestCase
{
    /**
     * @var ArgumentsPrepare
     */
    private $argumentsPrepare;

    /**
     * @var ArgumentInterfaceFactory|MockObject
     */
    private $argumentFactoryMock;

    protected function setUp(): void
    {
        $this->argumentFactoryMock = $this->createMock(ArgumentInterfaceFactory::class);
        $this->argumentsPrepare = new ArgumentsPrepare($this->argumentFactoryMock);
    }

    public function testExecuteEmptyArray()
    {
        $this->argumentFactoryMock->expects($this->never())
            ->method('create');

        $this->assertEquals([], $this->argumentsPrepare->execute([]));
    }

    public function testExecuteSimpleType()
    {
        $argumentName = 'tableName';
        $argumentXsiType = 'string';
        $argumentValue = 'catalog_product_entity';
        $argumentMock = $this->createMock(ArgumentInterface::class);

        $this->argumentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($argumentMock);
        $argumentMock->expects($this->once())
            ->method('setName')
            ->with($argumentName);
        $argumentMock->expects($this->once())
            ->method('setType')
            ->with($argumentXsiType);
        $argumentMock->expects($this->once())
            ->method('setValue')
            ->with($argumentValue);

        $argumentMock->expects($this->once())
            ->method('getType')
            ->willReturn($argumentXsiType);

        $this->assertEquals(
            [$argumentMock],
            $this->argumentsPrepare->execute(
                [
                    [
                        'name' => $argumentName,
                        'xsi:type' => $argumentXsiType,
                        'value' => $argumentValue
                    ]
                ]
            )
        );
    }

    public function testExecuteArray()
    {
        $argArrayName = 'someArrayArg';
        $argArrayXsiType = 'array';
        $argArrayMock = $this->createMock(ArgumentInterface::class);

        $argItemName = 'someArrayItemArg';
        $argItemXsiType = 'string';
        $argItemValue = 'someStringValue';
        $argItemMock = $this->createMock(ArgumentInterface::class);

        $this->argumentFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($argArrayMock, $argItemMock);

        $argArrayMock->expects($this->once())
            ->method('setName')
            ->with($argArrayName);
        $argArrayMock->expects($this->once())
            ->method('setType')
            ->with($argArrayXsiType);
        $argArrayMock->expects($this->once())
            ->method('getType')
            ->willReturn($argArrayXsiType);
        $argArrayMock->expects($this->once())
            ->method('setItems')
            ->with([$argItemMock]);

        $argItemMock->expects($this->once())
            ->method('setName')
            ->with($argItemName);
        $argItemMock->expects($this->once())
            ->method('setType')
            ->with($argItemXsiType);
        $argItemMock->expects($this->once())
            ->method('setValue')
            ->with($argItemValue);
        $argItemMock->expects($this->once())
            ->method('getType')
            ->willReturn($argItemXsiType);

        $this->assertEquals(
            [$argArrayMock],
            $this->argumentsPrepare->execute(
                [
                    [
                        'name' => $argArrayName,
                        'xsi:type' => $argArrayXsiType,
                        'item' => [
                            [
                                'name' => $argItemName,
                                'xsi:type' => $argItemXsiType,
                                'value' => $argItemValue
                            ]
                        ]
                    ]
                ]
            )
        );
    }
}
