<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Test\Unit\Export\Action\Conclusion;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\PostProcessing\ProcessorInterface;
use Amasty\ExportCore\Export\Action\Conclusion\PostProcessingAction;
use Amasty\ExportCore\Export\PostProcessing\PostProcessingProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ExportCore\Export\Action\Conclusion\PostProcessingAction
 */
class PostProcessingActionTest extends TestCase
{
    /**
     * @var PostProcessingAction
     */
    private $postProcessingAction;

    /**
     * @var PostProcessingProvider|MockObject
     */
    private $postProcessingProviderMock;

    /**
     * @var ExportProcessInterface|MockObject
     */
    private $exportProcessInterfaceMock;

    /**
     * @var ProfileConfigInterface|MockObject
     */
    private $profileConfigInterfaceMock;

    /**
     * @var ProcessorInterface|MockObject
     */
    private $processorInterfaceMock;

    public function setUp(): void
    {
        $this->postProcessingProviderMock = $this->createMock(PostProcessingProvider::class);
        $this->exportProcessInterfaceMock = $this->createMock(ExportProcessInterface::class);
        $this->profileConfigInterfaceMock = $this->createMock(ProfileConfigInterface::class);
        $this->processorInterfaceMock = $this->createMock(ProcessorInterface::class);

        $this->postProcessingAction = new PostProcessingAction($this->postProcessingProviderMock);
    }

    /**
     * @param array|null $postProcessors
     * @dataProvider initializeDataProvider
     */
    public function testInitialize(?array $postProcessors): void
    {
        $this->initInitialize($postProcessors);

        $this->postProcessingAction->initialize($this->exportProcessInterfaceMock);
    }

    private function initInitialize(?array $postProcessors): void
    {
        $this->exportProcessInterfaceMock->expects($this->once())
            ->method('getProfileConfig')
            ->willReturn($this->profileConfigInterfaceMock);
        $this->profileConfigInterfaceMock->expects($this->once())
            ->method('getPostProcessors')
            ->willReturn($postProcessors);
        if ($postProcessors) {
            $this->postProcessingProviderMock->expects($this->once())
                ->method('getSortedPostProcessors')
                ->with($postProcessors)
                ->willReturn([$this->processorInterfaceMock]);
        }
    }

    public function testExecuteWithNoProcessors(): void
    {
        $this->postProcessingAction->execute($this->exportProcessInterfaceMock);
    }

    public function testExecuteWithProcessors(): void
    {
        $postProcessors = ['processor_type'];

        $this->initInitialize($postProcessors);
        $this->postProcessingAction->initialize($this->exportProcessInterfaceMock);

        $this->processorInterfaceMock->expects($this->once())
            ->method('process')
            ->with($this->exportProcessInterfaceMock)
            ->willReturnSelf();

        $this->postProcessingAction->execute($this->exportProcessInterfaceMock);
    }

    /**
     * Data provider for testInitialize
     *
     * @return array
     */
    public function initializeDataProvider(): array
    {
        return [
            'processorsExist' => [['processor_type']],
            'noProcessors' => [null]
        ];
    }
}
