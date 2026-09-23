<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Config;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Config\EntitySource\Xml as XmlEntitySource;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Config\EntityConfigProvider
 */
class EntityConfigProviderTest extends TestCase
{
    public const ENTITY_CODE = 'test_code';

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var ManagerInterface|MockObject
     */
    private $eventManagerMock;

    public function setUp(): void
    {
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);
        $this->entityConfigProvider = new EntityConfigProvider(
            $this->eventManagerMock,
            []
        );
    }

    /**
     * Data provider for getConfig
     * @return array
     */
    public function getConfigDataProvider(): array
    {
        return [
            'validEntityCode' => [
                [
                    'same_code',
                    self::ENTITY_CODE
                ],
                self::ENTITY_CODE,
                1
            ],
            'invalidEntityCode' => [
                [
                    self::ENTITY_CODE
                ],
                'invalid_code',
                false
            ],
            'emptyEntityCode' => [
                [
                    self::ENTITY_CODE,
                    'same_code'
                ],
                '',
                []
            ]
        ];
    }

    /**
     * @param array $entityCodes
     * @param string $entityCode
     * @param array|int|bool $expectedResult
     * @dataProvider getConfigDataProvider
     */
    public function testGetConfig(
        array $entityCodes,
        string $entityCode,
        $expectedResult
    ) {
        $entities = [];
        foreach ($entityCodes as $item) {
            $entityMock = $this->createMock(EntityConfigInterface::class);
            $entityMock->expects($this->any())
                ->method('getEntityCode')
                ->willReturn($item);
            $entities[] = $entityMock;
        }

        $this->setEntitySourceMocks($entities);
        $this->eventManagerMock->expects($this->exactly(count($entityCodes)))
            ->method('dispatch')
            ->with('amimport_entity_loaded');

        if (is_int($expectedResult)) {
            $expectedResult = $entities[$expectedResult];
        } elseif (is_array($expectedResult)) {
            $expectedResult = array_combine($entityCodes, $entities);
        }

        $this->assertSame($expectedResult, $this->entityConfigProvider->getConfig($entityCode));
    }

    public function testGetConfigFromCache()
    {
        $entityMock = $this->createMock(EntityConfigInterface::class);
        $entityMock->expects($this->any())
            ->method('getEntityCode')
            ->willReturn(self::ENTITY_CODE);
        $this->setEntitySourceMocks([$entityMock]);

        $entityCacheMock = $this->createMock(EntityConfigInterface::class);
        $entityCacheMock->expects($this->any())
            ->method('getEntityCode')
            ->willReturn(self::ENTITY_CODE);

        $reflection = new \ReflectionClass(EntityConfigProvider::class);

        $entitiesConfigProp = $reflection->getProperty('entitiesConfig');
        $entitiesConfigProp->setAccessible(true);
        $entitiesConfigProp->setValue($this->entityConfigProvider, [self::ENTITY_CODE => $entityCacheMock]);

        $result = $this->entityConfigProvider->getConfig(self::ENTITY_CODE);
        $this->assertSame($entityCacheMock, $result);
    }

    public function testGetConfigAlreadyExists()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Entity "' . self::ENTITY_CODE . '" already exists.');
        $entityMock = $this->createMock(EntityConfigInterface::class);
        $entityMock->expects($this->any())
            ->method('getEntityCode')
            ->willReturn(self::ENTITY_CODE);
        $this->setEntitySourceMocks([$entityMock, $entityMock]);

        $this->entityConfigProvider->getConfig(self::ENTITY_CODE);
    }

    public function testGetWrongEntityCode()
    {
        $entityCode = 'invalid_code';
        $this->expectException('LogicException');
        $this->expectExceptionMessage('No entity config found for entity "' . $entityCode . '"');
        $entityMock = $this->createMock(EntityConfigInterface::class);
        $entityMock->expects($this->any())
            ->method('getEntityCode')
            ->willReturn(self::ENTITY_CODE);
        $this->setEntitySourceMocks([$entityMock]);

        $this->entityConfigProvider->get($entityCode);
    }

    /**
     * @param EntityConfigInterface[]|MockObject[] $entities
     * @return void
     * @throws \ReflectionException
     */
    private function setEntitySourceMocks(array $entities)
    {
        $xmlEntitySourceMock = $this->createMock(XmlEntitySource::class);
        $xmlEntitySourceMock->expects($this->any())
            ->method('get')
            ->willReturn($entities);
        $entitySources = ['xml' => $xmlEntitySourceMock];

        $reflection = new \ReflectionClass(EntityConfigProvider::class);

        $entitySourcesProp = $reflection->getProperty('entitySources');
        $entitySourcesProp->setAccessible(true);
        $entitySourcesProp->setValue($this->entityConfigProvider, $entitySources);
    }
}
