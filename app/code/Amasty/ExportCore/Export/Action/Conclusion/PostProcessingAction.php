<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Action\Conclusion;

use Amasty\ExportCore\Api\ActionInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\PostProcessing\ProcessorInterface;
use Amasty\ExportCore\Export\PostProcessing\PostProcessingProvider;

class PostProcessingAction implements ActionInterface
{
    /**
     * @var PostProcessingProvider
     */
    private $postProcessingProvider;

    /**
     * @var ProcessorInterface[]
     */
    private $processors = [];

    public function __construct(
        PostProcessingProvider $postProcessingProvider
    ) {
        $this->postProcessingProvider = $postProcessingProvider;
    }

    public function initialize(ExportProcessInterface $exportProcess)
    {
        $profileProcessors = $exportProcess->getProfileConfig()->getPostProcessors() ?? [];
        if ($profileProcessors) {
            $this->processors = $this->postProcessingProvider->getSortedPostProcessors($profileProcessors);
        }
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        foreach ($this->processors as $processor) {
            $processor->process($exportProcess);
        }
    }
}
