<?php

declare(strict_types=1);

namespace CyberSolutionsLLC\CategoryTiles\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\StoreManagerInterface;

class ImageChooser extends Template
{
    protected $_template = 'CyberSolutionsLLC_CategoryTiles::widget/image_upload.phtml';

    private StoreManagerInterface $storeManager;

    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $value = (string) $element->getValue();

        $this->setData('element_id', $element->getHtmlId());
        $this->setData('element_name', $element->getName());
        $this->setData('element_value', $value);

        // Render template first (uses element_value), then clear Label's visible text
        $html = $this->toHtml();
        $element->setData('value', '');
        $element->setData('after_element_html', $html);

        return $element;
    }

    public function getElementId(): string
    {
        return (string) $this->getData('element_id');
    }

    public function getElementName(): string
    {
        return (string) $this->getData('element_name');
    }

    public function getElementValue(): string
    {
        return (string) $this->getData('element_value');
    }

    public function getUploadUrl(): string
    {
        return $this->getUrl('categorytiles/widget/imageUpload');
    }

    public function getMediaUrl(): string
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
