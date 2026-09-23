<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Test\Unit\Import\Action\DataPrepare\Cleanup;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Model\Batch\BatchRepository;
use Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\BatchCleaner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ImportCore\Import\Action\DataPrepare\Cleanup\BatchCleaner
 */
class BatchCleanerTest extends TestCase
{
    /**
     * @var BatchCleaner
     */
    private $batchCleaner;

    /**
     * @var BatchRepository|MockObject
     */
    private $batchRepositoryMock;

    protected function setUp(): void
    {
        $this->batchRepositoryMock = $this->createMock(BatchRepository::class);
        $this->batchCleaner = new BatchCleaner($this->batchRepositoryMock);
    }

    public function testClean()
    {
        $identity = 'a098ac2b-1ab4-41d7-890e-5ae4d9bce09e';
        $importProcessMock = $this->createMock(ImportProcessInterface::class);

        $importProcessMock->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);
        $this->batchRepositoryMock->expects($this->once())
            ->method('cleanup')
            ->with($identity);

        $this->batchCleaner->clean($importProcessMock);
    }
}
