<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\Import\Import;

use Amasty\ImportCore\Api\BehaviorInterface;
use Amasty\ImportCore\Api\Config\Entity\BehaviorInterface as ConfigEntityBehaviorInterface;
use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Import\Action\Import\Import\BehaviorProvider;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface;
use Amasty\ImportExportCore\Config\ConfigClass\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\Import\Import\BehaviorProvider
 */
class BehaviorProviderTest extends TestCase
{
    public const BEHAVIOR_CODE = 'test';
    public const ENTITY_CODE = 'test';

    /**
     * @var BehaviorProvider
     */
    private $provider;

    /**
     * @var EntityConfigProvider|MockObject
     */
    private $entityConfigProviderMock;

    /**
     * @var Factory|MockObject
     */
    private $configClassFactoryMock;

    public function setUp(): void
    {
        $this->entityConfigProviderMock = $this->createPartialMock(EntityConfigProvider::class, ['get']);
        $this->configClassFactoryMock = $this->createPartialMock(Factory::class, ['createObject']);

        $this->provider = new BehaviorProvider(
            $this->configClassFactoryMock,
            $this->entityConfigProviderMock
        );
    }

    public function testGetBehaviorNoBehaviorCode()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Import behavior is not specified for entity ' . self::ENTITY_CODE);

        $this->provider->getBehavior(self::ENTITY_CODE, '');
    }

    public function testGetBehaviorNoConfigClass()
    {
        $entityConfigMock = $this->createEntityConfigMock(self::BEHAVIOR_CODE, null);
        $this->entityConfigProviderMock->expects($this->once())->method('get')
            ->willReturn($entityConfigMock);

        $this->expectException('LogicException');
        $this->expectExceptionMessage('Behavior "' . self::BEHAVIOR_CODE . '" has no class');

        $this->provider->getBehavior(self::ENTITY_CODE, self::BEHAVIOR_CODE);
    }

    public function testGetBehaviorSuccess()
    {
        $configClassMock = $this->createMock(ConfigClassInterface::class);
        $behaviorMock = $this->createMock(BehaviorInterface::class);
        $entityConfigMock = $this->createEntityConfigMock(self::BEHAVIOR_CODE, $configClassMock);

        $this->entityConfigProviderMock->expects($this->once())->method('get')
            ->willReturn($entityConfigMock);
        $this->configClassFactoryMock->expects($this->once())->method('createObject')
            ->with($configClassMock)
            ->willReturn($behaviorMock);

        $this->provider->getBehavior(self::ENTITY_CODE, self::BEHAVIOR_CODE);
    }

    public function testGetBehaviorConfigParent()
    {
        $entityConfigMock = $this->createEntityConfigMock(self::BEHAVIOR_CODE . '2', null);
        $this->entityConfigProviderMock->expects($this->once())->method('get')
            ->willReturn($entityConfigMock);

        $this->provider->getBehaviorConfig(self::ENTITY_CODE, self::BEHAVIOR_CODE, true);
    }

    /**
     * @param string $behaviorCode
     * @param ConfigClassInterface|null $configClass
     * @return MockObject
     */
    private function createEntityConfigMock(
        string $behaviorCode,
        ?ConfigClassInterface $configClass
    ): MockObject {
        $behaviorMock = $this->createConfiguredMock(
            ConfigEntityBehaviorInterface::class,
            [
                'getCode' => $behaviorCode,
                'getConfigClass' => $configClass,
                'getExecuteOnCodes' => [self::BEHAVIOR_CODE]
            ]
        );

        return $this->createConfiguredMock(
            EntityConfigInterface::class,
            [
                'getBehaviors' => [$behaviorMock]
            ]
        );
    }
}
