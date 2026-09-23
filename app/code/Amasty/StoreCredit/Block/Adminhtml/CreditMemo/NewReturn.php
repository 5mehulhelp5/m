<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Block\Adminhtml\CreditMemo;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Creditmemo;

class NewReturn extends Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PriceCurrencyInterface
     */
    private $currency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Registry $coreRegistry,
        Template\Context $context,
        PriceCurrencyInterface $currency,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->currency = $currency;
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $memo = $this->getCreditMemo();
        if (!$this->configProvider->isEnabled()
            || !($memo && $memo->getCustomerId())
        ) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return bool
     */
    public function isUseStoreCredit()
    {
        return $this->getCreditMemo() && $this->getCreditMemo()->getData(SalesFieldInterface::AMSC_USE);
    }

    /**
     * @return float|int
     */
    public function getMaxStoreCredit()
    {
        if ($memo = $this->getCreditMemo()) {
            if ($memo->getData('amstorecredit_base_amount') !== null) {
                return $this->currency->roundPrice($memo->getData('amstorecredit_base_amount'));
            }
            return $this->currency->roundPrice($memo->getBaseGrandTotal());
        }

        return 0;
    }

    /**
     * @return Creditmemo|null
     */
    public function getCreditMemo(): ?Creditmemo
    {
        return $this->coreRegistry->registry('current_creditmemo');
    }
}
