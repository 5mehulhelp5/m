<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Form;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FileResolver\FileResolverConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Config\RelationConfigProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class FileResolver implements FormInterface
{
    /**
     * @var FileResolverConfigInterface
     */
    private FileResolverConfigInterface $fileResolverConfig;

    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @var EntityConfigProvider
     */
    private EntityConfigProvider $entityConfigProvider;

    /**
     * @var RelationConfigProvider
     */
    private RelationConfigProvider $relationConfigProvider;

    public function __construct(
        FileResolverConfigInterface $fileResolverConfig,
        ObjectManagerInterface $objectManager,
        ?EntityConfigProvider $entityConfigProvider = null,
        ?RelationConfigProvider $relationConfigProvider = null
    ) {
        $this->fileResolverConfig = $fileResolverConfig;
        $this->objectManager = $objectManager;
        $this->entityConfigProvider = $entityConfigProvider
            ?: ObjectManager::getInstance()->get(EntityConfigProvider::class);
        $this->relationConfigProvider = $relationConfigProvider
            ?: ObjectManager::getInstance()->get(RelationConfigProvider::class);
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = [
            'file_config' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => (isset($arguments['label'])
                                ? __($arguments['label'])
                                : __('Import Source')),
                            'componentType' => 'fieldset',
                            'visible' => true,
                            'dataScope' => '',
                            'additionalClasses' => 'amimportcore-import-source',
                        ]
                    ]
                ],
                'children' => [
                    'file_source_type' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Import Source'),
                                    'notice' => __(
                                        'Please note that only certain file types are allowed for different '
                                        . 'import sources. Options with unavailable file types will be inactive '
                                        . 'in the file type selection setting for the selected source.'
                                    ),
                                    'visible' => true,
                                    'dataScope' => 'file_resolver_type',
                                    'validation' => [
                                        'required-entry' => true
                                    ],
                                    'dataType' => 'select',
                                    'component' => 'Amasty_ImportCore/js/type-selector',
                                    'prefix' => 'file_',
                                    'formElement' => 'select',
                                    'componentType' => 'select',
                                    'additionalClasses' => 'amimportcore-field',
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

        $fileResolvers = $this->fileResolverConfig->all();

        foreach ($fileResolvers as $fileResolverType => $fileResolverConfig) {
            $result['file_config']['children']['file_source_type']['arguments']['data']['config']['options'][] = [
                'label' => $fileResolverConfig['name'], 'value' => $fileResolverType
            ];
            if (!($fileResolverMetaClass = $this->getFileResolverMetaClass($fileResolverType))) {
                continue;
            }

            $fileResolverMeta = $fileResolverMetaClass->getMeta($entityConfig, $arguments);
            if (!empty($fileResolverMeta)) {
                $result['file_config']['children']['file_' . $fileResolverType] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => '',
                                'collapsible' => false,
                                'opened' => true,
                                'visible' => true,
                                'componentType' => 'fieldset',
                                'dataScope' => ''
                            ]
                        ]
                    ],
                    'children' => $fileResolverMeta
                ];
            }
        }

        if (($arguments['useImagesFileDirectory'] ?? false) || $this->checkFileUploaderConfig($entityConfig)) {
            $imageFileDirectory = [
                'file_config' => [
                    'children' => [
                        'images_file_directory' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Import Files Directory'),
                                        'visible' => true,
                                        'dataScope' => 'images_file_directory',
                                        'dataType' => 'text',
                                        'formElement' => 'input',
                                        'componentType' => 'input',
                                        'tooltip' => ['description' => __('For import from file directory, please '
                                            . 'use relative path to Magento installation, e.g. var/import.<br/>'
                                            . 'You can also import files and images using links. To do this, please '
                                            . 'specify the links to the files in the import file. Please, note that if '
                                            . 'you import files using links, the files are temporarily stored in the '
                                            . 'Import Files Directory during the import process, so this field should '
                                            . 'be filled in.<br/>'
                                            . 'Please note that when you import an image with the same '
                                            . 'name that already exists in the system, then the image will be updated.')
                                        ],
                                        'additionalClasses' => 'amimportcore-field-upload'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $result = array_merge_recursive($result, $imageFileDirectory);
        }

        return $result;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if (!$profileConfig->getFileResolverType()) {
            return [];
        }

        $result = ['file_resolver_type' => $profileConfig->getFileResolverType()];
        $metaClass = $this->getFileResolverMetaClass($profileConfig->getFileResolverType());
        if ($metaClass) {
            $result = array_merge_recursive($result, $metaClass->getData($profileConfig));
        }

        $imagesFileDirectory = $profileConfig->getImagesFileDirectory();
        if ($imagesFileDirectory) {
            $result['images_file_directory'] = $imagesFileDirectory;
        }

        return $result;
    }

    public function prepareConfig(
        ProfileConfigInterface $profileConfig,
        RequestInterface $request
    ): FormInterface {
        $fileResolver = $request->getParam('file_resolver_type');
        if ($fileResolver) {
            $profileConfig->setFileResolverType($fileResolver);
            $metaClass = $this->getFileResolverMetaClass($fileResolver);
            if ($metaClass) {
                $metaClass->prepareConfig($profileConfig, $request);
            }
        }

        $imagesFileDirectory = $request->getParam('images_file_directory');
        if ($imagesFileDirectory) {
            $profileConfig->setImagesFileDirectory($imagesFileDirectory);
        }

        return $this;
    }

    /**
     * @param string $fileResolverType
     *
     * @return bool|FormInterface
     * @throws LocalizedException
     */
    private function getFileResolverMetaClass(string $fileResolverType)
    {
        $fileResolver = $this->fileResolverConfig->get($fileResolverType);
        if (!empty($fileResolver['metaClass'])) {
            $metaClass = $fileResolver['metaClass'];
            if (!is_subclass_of($metaClass, FormInterface::class)) {
                throw new LocalizedException(__('Wrong file resolver form class: %1', $metaClass));
            }

            return $this->objectManager->create($metaClass);
        }

        return false;
    }

    private function checkFileUploaderConfig(EntityConfigInterface $entityConfig): bool
    {
        if ($entityConfig->getFileUploaderConfig()) {
            return true;
        }

        foreach ($this->relationConfigProvider->get($entityConfig->getEntityCode()) as $relationConfig) {
            $subEntityConfig = $this->entityConfigProvider->get($relationConfig->getChildEntityCode());
            if ($this->checkFileUploaderConfig($subEntityConfig)) {
                return true;
            }
        }

        return false;
    }
}
