<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\PostProcessing;

use Amasty\ExportCore\Api\PostProcessing\PostProcessingConfigInterface;

class PostProcessingConfig implements PostProcessingConfigInterface
{
    /**
     * @var array
     */
    private $postProcessingConfig = [];

    public function __construct(array $postProcessingConfig)
    {
        $this->initializePostProcessingConfig($postProcessingConfig);
    }

    public function get(string $type): array
    {
        if (!isset($this->postProcessingConfig[$type])) {
            throw new \RuntimeException('Processor "' . $type . '" is not defined');
        }

        return $this->postProcessingConfig[$type];
    }

    public function all(): array
    {
        return $this->postProcessingConfig;
    }

    private function initializePostProcessingConfig(array $postProcessingConfig): void
    {
        foreach ($postProcessingConfig as $config) {
            if (!isset($config['code'], $config['processorClass'])) {
                throw new \LogicException('Export processor "' . $config['code'] . ' is not configured properly');
            }
            if (!isset($config['sortOrder'])) {
                $config['sortOrder'] = 0;
            }
            $this->postProcessingConfig[$config['code']] = $config;
        }

        uasort($this->postProcessingConfig, function ($first, $second) {
            return $first['sortOrder'] <=> $second['sortOrder'];
        });
    }
}
