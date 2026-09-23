<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Api\PostProcessing\PostProcessingConfigInterface;
use Amasty\ExportCore\Export\Config\ProfileConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class PostProcessing implements FormInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PostProcessingConfigInterface
     */
    private $postProcessingConfig;

    public function __construct(
        PostProcessingConfigInterface $postProcessingConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->postProcessingConfig = $postProcessingConfig;
    }

    /**
     * @throws LocalizedException
     */
    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = [
            'post_processing' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => (
                                isset($arguments['label']) ? __($arguments['label']) : __('Additional Actions')
                            ),
                            'additionalClasses' => 'amexportcore-post-processing',
                            'dataScope' => '',
                            'componentType' => 'fieldset',
                            'visible' => true,
                        ]
                    ]
                ],
                'children' => []
            ]
        ];

        $postProcessors = $this->postProcessingConfig->all();
        foreach ($postProcessors as $type => $config) {
            $metaClass = $this->getPostProcessorMetaClass($type);
            if ($metaClass && !empty($meta = $metaClass->getMeta($entityConfig, $arguments))) {
                $result['post_processing']['children'][$type] = $meta;
            } else {
                throw new LocalizedException(
                    __(
                        'Can\'t build Ui Component configuration for %1 post processor: No meta given',
                        $type
                    )
                );
            }
        }

        return $result;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if (!$profileConfig->getPostProcessors()) {
            return [];
        }

        $result = [];
        foreach ($profileConfig->getPostProcessors() as $postProcessorType) {
            $result[ProfileConfig::POST_PROCESSORS][$postProcessorType] = $postProcessorType;
            if ($metaClass = $this->getPostProcessorMetaClass($postProcessorType)) {
                //phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $result[ProfileConfig::POST_PROCESSORS] = array_merge(
                    $result[ProfileConfig::POST_PROCESSORS],
                    $metaClass->getData($profileConfig)
                );
            }
        }

        return $result;
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $postProcessors = $request->getParam('post_processors');
        if (empty($postProcessors) || !is_array($postProcessors)) {
            $postProcessors = [];
        } else {
            $postProcessors = array_keys($postProcessors);
        }
        if (!empty($postProcessors)) {
            $profileConfig->setPostProcessors($postProcessors);
            foreach ($postProcessors as $type) {
                if ($metaClass = $this->getPostProcessorMetaClass($type)) {
                    $metaClass->prepareConfig($profileConfig, $request);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $postProcessorType
     *
     * @return bool|FormInterface
     * @throws LocalizedException
     */
    private function getPostProcessorMetaClass(string $postProcessorType)
    {
        $config = $this->postProcessingConfig->get($postProcessorType);
        if (!empty($config['metaClass'])) {
            $metaClass = $config['metaClass'];
            if (!is_subclass_of($metaClass, FormInterface::class)) {
                throw new LocalizedException(__('Wrong post processor form class: %1', $metaClass));
            }

            return $this->objectManager->create($metaClass);
        }

        return false;
    }
}
