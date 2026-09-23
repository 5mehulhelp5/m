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
namespace Webkul\QuickPayForOrder\Controller\Checkout;

use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Webkul\QuickPayForOrder\Model\QuickPayConfigProvider;

class AddressesPost extends \Magento\Multishipping\Controller\Checkout\AddressesPost
{
    
    protected $quickPayConfigProvider;
    
    /**
     * Multishipping checkout process posted addresses
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_getCheckout()->getCustomerDefaultShippingAddress()) {
            $this->_redirect('*/checkout_address/newShipping');
            return;
        }
        try {
            if ($this->getRequest()->getParam('continue', false)) {
                $this->_getCheckout()->setCollectRatesFlag(true);
                $this->_getState()->setActiveStep(State::STEP_SHIPPING);
                $this->_getState()->setCompleteStep(State::STEP_SELECT_ADDRESSES);
                $this->_redirect('*/*/shipping');
            } elseif ($this->getRequest()->getParam('new_address')) {
                $this->_redirect('*/checkout_address/newShipping');
            } else {
                $this->_redirect('*/*/addresses');
            }

            $this->quickPayConfigProvider = $this->_objectManager->get(QuickPayConfigProvider::class);
            $quickPayQuoteId = null;
            $quickpayStatus = false;
            $currentQuoteId = $this->_getCheckout()->getQuote()->getId();
            $config = $this->quickPayConfigProvider->getConfig();

            if (empty($config)) {
                $quickpayStatus = false;
            }

            if (isset($config['quickPayQuoteId'])) {
                $quickPayQuoteId = $config['quickPayQuoteId'];
            }

            if ($quickPayQuoteId == $currentQuoteId) {
                $quickpayStatus = true;
            }
            
            if ($shipToInfo = $this->getRequest()->getPost('ship')) {
                if ($quickpayStatus) {
                    $shipToInfo = $this->updateShipInfo($shipToInfo);
                }
                $this->_getCheckout()->setShippingItemsInformation($shipToInfo);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/addresses');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Data saving problem'));
            $this->_redirect('*/*/addresses');
        }
    }

    public function updateShipInfo($shipToInfo)
    {
        $messageStatus = false;
        $quote = $this->_getCheckout()->getQuote();
        foreach ($shipToInfo as $key => $itemData) {
            foreach ($itemData as $quoteItemId => $data) {
                $origQty = 0;
                $quoteShippingAddressItems = $quote->getShippingAddressesItems();
                foreach ($quoteShippingAddressItems as $addressItem) {
                    if (isset($data['address']) && $addressItem->getCustomerAddressId() == $data['address']) {
                        $origQty = $addressItem->getQty();
                    }
                }
                if (isset($data['qty']) && $origQty && $data['qty'] != $origQty) {
                    if (!$messageStatus) {
                        $this->messageManager->addWarning(__("You are not allowed to update this quote items."));
                    }
                    $messageStatus = true;
                    $data['qty'] = $origQty;
                }
                $shipToInfo[$key][$quoteItemId] = $data;
            }
        }

        return $shipToInfo;
    }
}
