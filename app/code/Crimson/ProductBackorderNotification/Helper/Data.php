<?php

namespace Crimson\ProductBackorderNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_BACKORDER_DEFAULT_MESSAGE = 'catalog/backorder_notification/default_message';

    /**
     * @param $store
     * @return bool
     */
    public function getDefaultBackorderMessage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BACKORDER_DEFAULT_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
