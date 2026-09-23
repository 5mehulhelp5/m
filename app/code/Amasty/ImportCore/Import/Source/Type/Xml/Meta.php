<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Xml;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

class Meta implements FormInterface
{
    public const DATASCOPE = 'extension_attributes.xml_source.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    public function __construct(
        ConfigInterfaceFactory $configFactory
    ) {
        $this->configFactory = $configFactory;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = [
            'xml.item_path' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Data XPath'),
                            'dataType' => 'text',
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataScope' => self::DATASCOPE . 'item_path',
                            'formElement' => 'input',
                            'visible' => true,
                            'sortOrder' => 10,
                            'componentType' => 'field',
                            'notice' => __('Specify the path to the node, e.g. items/item.')
                        ]
                    ]
                ]
            ],
            'xml.xsl_template' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Xsl Template'),
                            'dataType' => 'text',
                            'formElement' => 'textarea',
                            'rows' => 20,
                            'component' => 'Amasty_ImportCore/js/form/element/codemirror',
                            'additionalClasses' => 'amimportcore-textarea -codemirror',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'xsl_template',
                            'codeMirrorConfig' => [
                                'mode' => 'application/xml'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $result;
    }

    public function prepareConfig(
        ProfileConfigInterface $profileConfig,
        RequestInterface $request
    ): FormInterface {
        $config = $this->configFactory->create();

        if (isset($request->getParam('extension_attributes')['xml_source']['item_path'])) {
            $config->setItemPath($request->getParam('extension_attributes')['xml_source']['item_path']);
        }
        if (isset($request->getParam('extension_attributes')['xml_source']['xsl_template'])) {
            $config->setXslTemplate($request->getParam('extension_attributes')['xml_source']['xsl_template']);
        }

        $profileConfig->getExtensionAttributes()->setXmlSource($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        $config = $profileConfig->getExtensionAttributes()->getXmlSource();
        if ($config) {
            return [
                'extension_attributes' => [
                    'xml_source' => [
                        'item_path' => $config->getItemPath(),
                        'xsl_template' => $config->getXslTemplate()
                    ]
                ]
            ];
        }

        return [];
    }
}
