<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\DataPrepare\Cleanup;

use Amasty\ImportCore\Api\Action\CleanerInterface;
use Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\CleanerProvider;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\CleanerProvider
 */
class CleanerProviderTest extends TestCase
{
    /**
     * @var CleanerProvider
     */
    private $cleanerProvider;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    protected function setUp(): void
    {
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->cleanerProvider = new CleanerProvider($this->objectManagerMock);
    }

    public function testGetCleaners()
    {
        $className = 'SomeCleaner';
        $cleanerMock = $this->createMock(CleanerInterface::class);

        $this->setCleanersProperty(
            ['custom_cleaner' => ['class' => $className]]
        );
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className)
            ->willReturn($cleanerMock);

        $this->assertEquals([$cleanerMock], $this->cleanerProvider->getCleaners('some_entity'));
    }

    public function testGetCleanersForSpecificEntity()
    {
        $entityCode = 'some_entity';
        $className = 'SomeCleaner';
        $cleanerMock = $this->createMock(CleanerInterface::class);

        $this->setCleanersProperty(
            [
                'custom_cleaner' => [
                    'class' => $className,
                    'entities' => [$entityCode]
                ]
            ]
        );
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className)
            ->willReturn($cleanerMock);

        $this->assertEquals([$cleanerMock], $this->cleanerProvider->getCleaners($entityCode));
    }

    public function testGetCleanersForUnspecifiedEntity()
    {
        $entityCode = 'some_entity';
        $className = 'SomeCleaner';

        $this->setCleanersProperty(
            [
                'custom_cleaner' => [
                    'class' => $className,
                    'entities' => [$entityCode]
                ]
            ]
        );
        $this->objectManagerMock->expects($this->never())
            ->method('create')
            ->with($className);

        $this->assertEquals([], $this->cleanerProvider->getCleaners('another_entity_code'));
    }

    public function testGetCleanersIncorrectEntitiesConfig()
    {
        $className = 'SomeCleaner';

        $this->setCleanersProperty(
            [
                'custom_cleaner' => [
                    'class' => $className,
                    'entities' => 'some_entity'
                ]
            ]
        );
        $this->objectManagerMock->expects($this->never())
            ->method('create')
            ->with($className);

        $this->assertEquals([], $this->cleanerProvider->getCleaners('another_entity_code'));
    }

    public function testGetCleanersNoClassNameInConfig()
    {
        $this->setCleanersProperty(['custom_cleaner' => []]);

        $this->expectExceptionMessage('Cleaner classname isn\'t specified.');
        $this->objectManagerMock->expects($this->never())
            ->method('create');

        $this->cleanerProvider->getCleaners('entity_code');
    }

    /**
     * Set 'cleaners' property value
     *
     * @param array $cleaners
     * @return void
     * @throws \ReflectionException
     */
    private function setCleanersProperty(array $cleaners)
    {
        $reflection = new \ReflectionClass(CleanerProvider::class);

        $cleanersProp = $reflection->getProperty('cleaners');
        $cleanersProp->setAccessible(true);
        $cleanersProp->setValue($this->cleanerProvider, $cleaners);
    }
}
