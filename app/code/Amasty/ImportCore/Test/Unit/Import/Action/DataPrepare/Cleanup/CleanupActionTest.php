<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\DataPrepare\Cleanup;

use Amasty\ImportCore\Api\Action\CleanerInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\CleanerProvider;
use Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\CleanupAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\CleanupAction
 */
class CleanupActionTest extends TestCase
{
    /**
     * @var CleanupAction
     */
    private $action;

    /**
     * @var CleanerProvider|MockObject
     */
    private $cleanerProviderMock;

    protected function setUp(): void
    {
        $this->cleanerProviderMock = $this->createMock(CleanerProvider::class);
        $this->action = new CleanupAction($this->cleanerProviderMock);
    }

    public function testExecute()
    {
        $importProcessMock = $this->createMock(ImportProcessInterface::class);
        $importProcessMock->expects($this->once())
            ->method('getBatchNumber')
            ->willReturn(1);
        $cleanerMock = $this->createMock(CleanerInterface::class);

        $reflection = new \ReflectionClass(CleanupAction::class);

        $cleanersProp = $reflection->getProperty('cleaners');
        $cleanersProp->setAccessible(true);
        $cleanersProp->setValue($this->action, [$cleanerMock]);

        $cleanerMock->expects($this->once())
            ->method('clean')
            ->with($importProcessMock);

        $this->action->execute($importProcessMock);
    }

    public function testInitialize()
    {
        $entityCode = 'some_entity';

        $importProcessMock = $this->createMock(ImportProcessInterface::class);
        $profileConfigMock = $this->createMock(ProfileConfigInterface::class);
        $cleanerMock = $this->createMock(CleanerInterface::class);

        $importProcessMock->expects($this->once())
            ->method('getProfileConfig')
            ->willReturn($profileConfigMock);
        $profileConfigMock->expects($this->once())
            ->method('getEntityCode')
            ->willReturn($entityCode);
        $this->cleanerProviderMock->expects($this->once())
            ->method('getCleaners')
            ->with($entityCode)
            ->willReturn([$cleanerMock]);

        $reflection = new \ReflectionClass(CleanupAction::class);

        $cleanersProp = $reflection->getProperty('cleaners');
        $cleanersProp->setAccessible(true);

        $this->action->initialize($importProcessMock);

        $this->assertEquals([$cleanerMock], $cleanersProp->getValue($this->action));
    }
}
