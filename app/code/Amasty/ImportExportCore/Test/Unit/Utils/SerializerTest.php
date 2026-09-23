<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Test\Unit\Utils;

use Amasty\ImportExportCore\Utils\Internal\ArrayToObjectConvert;
use Amasty\ImportExportCore\Utils\Serializer;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportExportCore\Utils\Serializer
 */
class SerializerTest extends TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ServiceOutputProcessor|MockObject
     */
    private $serviceOutputProcessorMock;

    /**
     * @var Json|MockObject
     */
    private $jsonSerializerMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var ArrayToObjectConvert|MockObject
     */
    private $arrayToObjectConvertMock;

    protected function setUp(): void
    {
        $this->serviceOutputProcessorMock = $this->createMock(ServiceOutputProcessor::class);
        $this->jsonSerializerMock = $this->createMock(Json::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->arrayToObjectConvertMock = $this->createMock(ArrayToObjectConvert::class);

        $this->serializer = new Serializer(
            $this->serviceOutputProcessorMock,
            $this->arrayToObjectConvertMock,
            $this->dataObjectHelperMock,
            $this->jsonSerializerMock
        );
    }

    public function testSerialize()
    {
        $data = ['field' => 'value'];
        $serializedData = '{"field":"value"}';
        $objectMock = $this->createMock(DummyDataObjectInterface::class);

        $this->serviceOutputProcessorMock->expects($this->once())
            ->method('convertValue')
            ->with($objectMock, DummyDataObjectInterface::class)
            ->willReturn($data);
        $this->jsonSerializerMock->expects($this->once())
            ->method('serialize')
            ->with($data)
            ->willReturn($serializedData);

        $this->assertEquals(
            $serializedData,
            $this->serializer->serialize($objectMock, DummyDataObjectInterface::class)
        );
    }

    public function testUnserialize()
    {
        $serializedData = '{"field":"value"}';
        $data = ['field' => 'value'];
        $objectMock = $this->createMock(DummyDataObjectInterface::class);

        $this->jsonSerializerMock->expects($this->once())
            ->method('unserialize')
            ->with($serializedData)
            ->willReturn($data);
        $this->arrayToObjectConvertMock->expects($this->once())
            ->method('convertValue')
            ->with($data, DummyDataObjectInterface::class)
            ->willReturn($objectMock);

        $this->assertEquals(
            $objectMock,
            $this->serializer->unserialize($serializedData, DummyDataObjectInterface::class)
        );
    }
}
