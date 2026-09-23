<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Data;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class MultipleWebsites extends Field
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setDisabled(false);
        if ((int)$this->scopeConfig->getValue(Data::XML_PATH_PRICE_SCOPE) === Data::PRICE_SCOPE_WEBSITE) {
            $element->setDisabled(true);
            $element->setValue(0);
        }

        return parent::_getElementHtml($element);
    }
}
