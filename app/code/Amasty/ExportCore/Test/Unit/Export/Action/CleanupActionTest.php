<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Test\Unit\Export\Action;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Export\Action\CleanupAction;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ExportCore\Export\Action\CleanupAction
 */
class CleanupActionTest extends TestCase
{
    /**
     * @var CleanupAction
     */
    private $action;

    /**
     * @var TmpFileManagement|MockObject
     */
    private $tmpMock;

    protected function setUp(): void
    {
        $this->tmpMock = $this->createMock(TmpFileManagement::class);
        $this->action = new CleanupAction($this->tmpMock);
    }

    public function testExecute()
    {
        $identity = 'string';

        $exportProcessMock = $this->createMock(ExportProcessInterface::class);

        $exportProcessMock->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);
        $this->tmpMock->expects($this->once())
            ->method('cleanFiles')
            ->with($identity);

        $this->action->execute($exportProcessMock);
    }
}
