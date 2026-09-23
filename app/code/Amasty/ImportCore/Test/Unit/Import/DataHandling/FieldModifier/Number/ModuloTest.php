<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier\Number;

use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Import\DataHandling\FieldModifier\Number\Modulo;
use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Number\Modulo
 */
class ModuloTest extends TestCase
{
    /**
     * @var Modulo
     */
    private $modifier;

    /**
     * @var ArgumentConverter|MockObject
     */
    private $argumentsConverterMock;

    protected function setUp(): void
    {
        $this->argumentsConverterMock = $this->createMock(ArgumentConverter::class);
        $this->modifier = new Modulo([], $this->argumentsConverterMock);
    }

    /**
     * @param mixed $value
     * @param array $config
     * @param mixed $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, array $config, $expectedResult)
    {
        $reflection = new \ReflectionClass(Modulo::class);

        $configProperty = $reflection->getProperty('config');
        $configProperty->setAccessible(true);
        $configProperty->setValue($this->modifier, $config);

        $this->assertEquals($expectedResult, $this->modifier->transform($value));
    }

    /**
     * @param array $requestData
     * @dataProvider prepareArgumentsEmptyDataProvider
     */
    public function testPrepareArgumentsEmpty(array $requestData)
    {
        $this->argumentsConverterMock->expects($this->never())
            ->method('valueToArguments');

        $this->assertEquals(
            [],
            $this->modifier->prepareArguments(
                $this->createMock(FieldInterface::class),
                $requestData
            )
        );
    }

    public function testPrepareArguments()
    {
        $inputValue = 3;
        $requestData = [
            'value' => [
                'input_value' => $inputValue
            ]
        ];

        $argumentMock = $this->createMock(ArgumentInterface::class);

        $this->argumentsConverterMock->expects($this->once())
            ->method('valueToArguments')
            ->with(
                (string)$inputValue,
                'input_value',
                'string'
            )->willReturn(
                [$argumentMock]
            );

        $this->assertEquals(
            [$argumentMock],
            $this->modifier->prepareArguments(
                $this->createMock(FieldInterface::class),
                $requestData
            )
        );
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            [5, [], 5],
            [5, ['some_field' => 'some_value'], 5],
            [5, ['input_value' => '3'], 2]
        ];
    }

    /**
     * Data provider for prepareArguments
     * @return array
     */
    public function prepareArgumentsEmptyDataProvider(): array
    {
        return [
            [[]],
            [['value' => []]],
            [
                [
                    'value' => [
                        'input_value' => []
                    ]
                ]
            ]
        ];
    }
}
