<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\PostProcessing;

use Amasty\ExportCore\Api\PostProcessing\ProcessorInterface;
use Magento\Framework\ObjectManagerInterface;

class PostProcessingProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PostProcessingConfig
     */
    private $postProcessingConfig;

    public function __construct(
        ObjectManagerInterface $objectManager,
        PostProcessingConfig $templateConfig
    ) {
        $this->objectManager = $objectManager;
        $this->postProcessingConfig = $templateConfig;
    }

    public function getProcessor(string $type): ProcessorInterface
    {
        $processorClass = $this->postProcessingConfig->get($type)['processorClass'];

        if (!is_subclass_of($processorClass, ProcessorInterface::class)) {
            throw new \RuntimeException('Wrong processor class: "' . $processorClass);
        }

        return $this->objectManager->create($processorClass);
    }

    /**
     * @param array $profileProcessors postProcessors from profile
     *
     * @return array
     */
    public function getSortedPostProcessors(array $profileProcessors): array
    {
        $postProcessorsConfig = [];
        foreach ($profileProcessors as $type) {
            $postProcessorsConfig[] = $this->postProcessingConfig->get($type);
        }
        uasort($postProcessorsConfig, function ($first, $second) {
            return $first['sortOrder'] <=> $second['sortOrder'];
        });

        $postProcessors = [];
        foreach ($postProcessorsConfig as $config) {
            $type = $config['code'];
            $postProcessors[$type] = $this->getProcessor($type);
        }

        return $postProcessors;
    }
}
