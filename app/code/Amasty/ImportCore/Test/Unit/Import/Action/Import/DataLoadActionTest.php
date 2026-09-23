<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\Import\Import;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\ImportResultInterface;
use Amasty\ImportCore\Import\Action\Import\DataLoadAction;
use Amasty\ImportCore\Model\Batch\Batch;
use Amasty\ImportCore\Model\Batch\BatchRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\Import\DataLoadAction
 */
class DataLoadActionTest extends TestCase
{
    public const IDENTITY = 'a098ac2b-1ab4-41d7-890e-5ae4d9bce09e';

    /**
     * @var DataLoadAction
     */
    private $dataLoadAction;

    /**
     * @var BatchRepository|MockObject
     */
    private $batchRepositoryMock;

    public function setUp(): void
    {
        $this->batchRepositoryMock = $this->createMock(BatchRepository::class);
        $this->dataLoadAction = new DataLoadAction($this->batchRepositoryMock);
    }

    public function testExecute()
    {
        $batchId = 1;
        $batchData = [['entity_id' => 2]];

        $importProcessMock = $this->createMock(ImportProcessInterface::class);
        $batchMock = $this->createMock(Batch::class);

        $importProcessMock->expects($this->any())
            ->method('getIdentity')
            ->willReturn(self::IDENTITY);
        $this->batchRepositoryMock->expects($this->once())
            ->method('fetchBatch')
            ->with(self::IDENTITY)
            ->willReturn($batchMock);
        $batchMock->expects($this->once())
            ->method('getId')
            ->willReturn($batchId);
        $batchMock->expects($this->once())
            ->method('__call')
            ->with('getBatchData')
            ->willReturn($batchData);
        $importProcessMock->expects($this->once())
            ->method('setData')
            ->with($batchData);
        $importProcessMock->expects($this->once())
            ->method('canFork')
            ->willReturn(false);

        $this->dataLoadAction->execute($importProcessMock);
    }

    public function testExecuteNoBatch()
    {
        $importProcessMock = $this->createMock(ImportProcessInterface::class);
        $importResultMock = $this->createMock(ImportResultInterface::class);
        $batchMock = $this->createMock(Batch::class);

        $importProcessMock->expects($this->any())
            ->method('getIdentity')
            ->willReturn(self::IDENTITY);
        $this->batchRepositoryMock->expects($this->once())
            ->method('fetchBatch')
            ->with(self::IDENTITY)
            ->willReturn($batchMock);
        $batchMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $importProcessMock->expects($this->once())
            ->method('getImportResult')
            ->willReturn($importResultMock);
        $importResultMock->expects($this->once())
            ->method('terminateImport');

        $this->dataLoadAction->execute($importProcessMock);
    }
}
