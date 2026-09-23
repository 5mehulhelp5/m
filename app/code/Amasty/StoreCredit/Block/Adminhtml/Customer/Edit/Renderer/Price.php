<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Block\Adminhtml\Customer\Edit\Renderer;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Amasty\StoreCredit\Model\PriceConverter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Block\Context;

class Price extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * @var PriceConverter
     */
    private $priceConverter;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        Context $context,
        array $data = [],
        ?PriceConverter $priceConverter = null //todo: move to not optional
    ) {
        parent::__construct($context, $data);
        $this->priceConverter = $priceConverter?? ObjectManager::getInstance()->get(PriceConverter::class);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        return $this->priceConverter->formatPrice((float)$row->getData(HistoryInterface::STORE_CREDIT_BALANCE));
    }
}
