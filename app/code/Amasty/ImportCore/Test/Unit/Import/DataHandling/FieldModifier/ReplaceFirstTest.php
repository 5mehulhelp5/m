<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Config\Profile\FieldInterface;
use Amasty\ImportCore\Import\DataHandling\FieldModifier\ReplaceFirst;
use Amasty\ImportCore\Import\Utils\Config\ArgumentConverter;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\ReplaceFirst
 */
class ReplaceFirstTest extends TestCase
{
    /**
     * @var ReplaceFirst
     */
    private $modifier;

    /**
     * @var ArgumentConverter|MockObject
     */
    private $argumentsConverterMock;

    protected function setUp(): void
    {
        $this->argumentsConverterMock = $this->createMock(ArgumentConverter::class);
        $this->modifier = new ReplaceFirst([], $this->argumentsConverterMock);
    }

    /**
     * @param mixed $value
     * @param array $config
     * @param mixed $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, array $config, $expectedResult)
    {
        $reflection = new \ReflectionClass(ReplaceFirst::class);

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
        $fromInputValue = 'some text';
        $toInputValue = 'new text';
        $requestData = [
            'value' => [
                'from_input_value' => $fromInputValue,
                'to_input_value' => $toInputValue
            ]
        ];

        $argumentMock = $this->createMock(ArgumentInterface::class);

        $this->argumentsConverterMock->expects($this->exactly(2))
            ->method('valueToArguments')
            ->willReturnMap([
                [$fromInputValue, 'from_input_value', 'string', [$argumentMock]],
                [$toInputValue, 'to_input_value', 'string', [$argumentMock]]
            ]);

        $this->assertEquals(
            [$argumentMock, $argumentMock],
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
            ['Some text', [], 'Some text'],
            ['Some text', ['input_value' => 'some_value'], 'Some text'],
            [123, ['from_input_value' => 'Some'], 123],
            ['Some text', ['from_input_value' => 1], 'Some text'],
            ['Some text Some a', ['from_input_value' => 'Some'], ' text Some a'],
            ['Some text Some a', ['from_input_value' => 'Some', 'to_input_value' => 'new'], 'new text Some a']
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
        ];
    }
}
