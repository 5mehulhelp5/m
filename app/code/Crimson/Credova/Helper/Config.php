<?php

namespace Crimson\Credova\Helper;

class Config extends \Credova\Financial\Helper\Config
{

    public function getCredovaProductPageDisplayRegardlessOfProductSetting($scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode = null): string
    {
        return $this->scopeConfig
            ->getValue('payment/credovafinancial/display_product_page_no_product_config', $scopeType, $scopeCode);
    }
}
