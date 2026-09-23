<?php
declare(strict_types=1);

namespace CyberSolutionsLLC\CheckoutDefaultCountry\Observer;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Address;

class QuoteAddressSaveBeforeObserver implements ObserverInterface
{
    private string $defaultCountry;

    public function __construct(DirectoryHelper $directoryHelper)
    {
        $this->defaultCountry = $directoryHelper->getDefaultCountry() ?: 'US';
    }

    public function execute(Observer $observer): void
    {
        /** @var Address $address */
        $address = $observer->getEvent()->getData('quote_address');
        if ($address instanceof Address && !$address->getCountryId()) {
            $address->setCountryId($this->defaultCountry);
        }
    }
}
