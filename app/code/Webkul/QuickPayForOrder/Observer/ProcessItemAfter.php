<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_QuickPayForOrder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\QuickPayForOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Session;

class ProcessItemAfter implements ObserverInterface
{
    
    protected $_session;
    
    /**
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->_session = $session;
    }

    /**
     * Process Item handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getData();
        $session = $data['session'];
        $items = $session->getQuote()->getAllItems();
        $additionalOptions = (string)$this->_session->getAdditionalOptions();
        $optionsData = json_decode($additionalOptions, true);
        if (isset($optionsData['productId'])) {
            foreach ($items as $item) {
                if ($optionsData['productId'] == $item->getProductId()) {
                    if (isset($optionsData['options']) && !empty($optionsData['options'])) {
                        $price = 0;
                        foreach ($optionsData['options'] as $option) {
                            $price = $price + (float)$option['value'];
                        }
                        $item->addOption([
                            'product_id' => $item->getProductId(),
                            'code' => 'additional_options',
                            'value' => json_encode($optionsData['options'])
                        ]);
                        $item->setCustomPrice($price);
                        $item->setOriginalCustomPrice($price);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                    $this->_session->unsAdditionalOptions();
                    break;
                }
            }
        }
    }
}
