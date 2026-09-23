<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Import\DataHandling\FieldModifier\Unserialize as UnserializeModifier;
use Magento\Framework\Serialize\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\DataHandling\FieldModifier\Unserialize
 */
class UnserializeTest extends TestCase
{
    /**
     * @var UnserializeModifier
     */
    private $modifier;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializerMock;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->modifier = new UnserializeModifier([], $this->serializerMock);
    }

    /**
     * @param mixed $value
     * @param mixed $unserializedValue
     * @param mixed $expectedResult
     * @dataProvider transformDataProvider
     */
    public function testTransform($value, $unserializedValue, $expectedResult)
    {
        $this->serializerMock->expects($this->once())
            ->method('unserialize')
            ->willReturnMap([
                [$value, $unserializedValue]
            ]);
        $this->assertEquals($expectedResult, $this->modifier->transform($value));
    }

    /**
     * Data provider for transform
     * @return array
     */
    public function transformDataProvider(): array
    {
        return [
            'invalid_value' => ['Some text', false, 'Some text'],
            'serialized_value' => ['{"test":"value"}', ['test' => 'value'], ['test' => 'value']]
        ];
    }
}
