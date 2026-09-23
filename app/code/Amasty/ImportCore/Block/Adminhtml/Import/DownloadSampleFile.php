<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DownloadSampleFile implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Download Sample File'),
            'class' => 'download',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'amimport_import_form.amimport_import_form',
                                'actionName' => 'downloadSampleFile',
                                'params' => [
                                    [
                                        'url' => $this->urlBuilder->getUrl('amimport/import/download')
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
            ],
            'on_click' => '',
            'sort_order' => 25
        ];
    }
}
