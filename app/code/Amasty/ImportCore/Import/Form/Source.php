<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Form;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Api\Source\SourceConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class Source implements FormInterface
{
    public const CORE_FILE_CONFIG_PATH = 'amimport_import_form.amimport_import_form.file_config';

    /**
     * @var SourceConfigInterface
     */
    private $sourceConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $dependedOptionsMap;

    public function __construct(
        SourceConfigInterface $sourceConfig,
        ObjectManagerInterface $objectManager,
        array $dependedOptionsMap = []
    ) {
        $this->sourceConfig = $sourceConfig;
        $this->objectManager = $objectManager;
        $this->dependedOptionsMap = $dependedOptionsMap;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = [
            'source_config' =>  [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => (isset($arguments['label'])
                                ? __($arguments['label'])
                                : __('Import File')),
                            'componentType' => 'fieldset',
                            'additionalClasses' => 'amimportcore-fieldset',
                            'visible' => true,
                            'dataScope' => ''
                        ]
                    ]
                ],
                'children' => [
                    'source' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Import File Type'),
                                    'notice' => __(
                                        'Options will be available according to the selected import source.'
                                    ),
                                    'visible' => true,
                                    'dataScope' => 'source_type',
                                    'source' => 'source_type',
                                    'validation' => [
                                        'required-entry' => true
                                    ],
                                    'dataType' => 'select',
                                    'component' => 'Amasty_ImportCore/js/form/element/file-type-select',
                                    'elementTmpl' => 'Amasty_ImportCore/form/element/disabled-select',
                                    'prefix' => 'source_',
                                    'fileProviderPath' => $arguments['fileProviderPath'] ?? self::CORE_FILE_CONFIG_PATH,
                                    'dependedOptionsMap' => $this->dependedOptionsMap,
                                    'formElement' => 'select',
                                    'componentType' => 'select',
                                    'additionalClasses' => 'amimportcore-field -sourceconfig',
                                    'options' => [
                                        ['label' => __('Please Select...'), 'value' => '']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $sources = $this->sourceConfig->all();

        foreach ($sources as $sourceType => $sourceConfig) {
            $result['source_config']['children']['source']['arguments']['data']['config']['options'][] = [
                'label' => $sourceConfig['name'], 'value' => $sourceType
            ];

            $children = [];
            $metaClass = $this->getSourceMetaClass($sourceType);
            if ($metaClass) {
                $children = array_merge_recursive($children, $metaClass->getMeta($entityConfig));
            }

            if (!empty($children)) {
                $result['source_config']['children']['source_' . $sourceType] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => '',
                                'collapsible' => false,
                                'opened' => true,
                                'visible' => true,
                                'componentType' => 'fieldset',
                                'additionalClasses' => 'amimportcore-fieldset-container -child',
                                'dataScope' => ''
                            ]
                        ]
                    ],
                    'children' => $children
                ];
            }
        }

        return $result;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if (!$profileConfig->getSourceType()) {
            return [];
        }

        $result = ['source_type' => $profileConfig->getSourceType()];
        $metaClass = $this->getSourceMetaClass($profileConfig->getSourceType());
        if ($metaClass) {
            $result = array_merge_recursive($result, $metaClass->getData($profileConfig));
        }

        return $result;
    }

    public function prepareConfig(
        ProfileConfigInterface $profileConfig,
        RequestInterface $request
    ): FormInterface {
        $source = $request->getParam('source_type');
        if ($source) {
            $profileConfig->setSourceType($source);
            $metaClass = $this->getSourceMetaClass($source);
            if ($metaClass) {
                $metaClass->prepareConfig($profileConfig, $request);
            }
        }

        return $this;
    }

    /**
     * @param string $sourceType
     *
     * @return bool|FormInterface
     * @throws LocalizedException
     */
    private function getSourceMetaClass(string $sourceType)
    {
        $source = $this->sourceConfig->get($sourceType);
        if (!empty($source['metaClass'])) {
            $metaClass = $source['metaClass'];
            if (!is_subclass_of($metaClass, FormInterface::class)) {
                throw new LocalizedException(__('Wrong source form class: %1', $metaClass));
            }

            return $this->objectManager->create($metaClass);
        }

        return false;
    }
}
