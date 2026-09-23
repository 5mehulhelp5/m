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
namespace Webkul\QuickPayForOrder\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\MailException;
use Magento\Customer\Api\Data\CustomerInterface;
use Webkul\QuickPayForOrder\Model\QuickPayOrdersFactory;

/**
 * Webkul QuickPayForOrder Helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SKU = 'wk_service';

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyModelFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_storeManager;
    protected $_messageManager;
    protected $_urlHelper;
    protected $_encryptor;
    protected $priceHelper;
    protected $productRepository;
    protected $addressFactory;
    protected $localeCurrency;
    protected $accountManager;
    protected $_quoteFactory;
    protected $_quickPayOrdersFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Url $urlHelper
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyModelFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Customer\Model\AccountManagement $accountManager
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Url $urlHelper,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyModelFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Customer\Model\AccountManagement $accountManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        QuickPayOrdersFactory $quickPayOrdersFactory
    ) {
        parent::__construct($context);
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_urlHelper = $urlHelper;
        $this->_encryptor = $encryptor;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
        $this->priceHelper = $priceHelper;
        $this->productRepository = $productRepository;
        $this->addressFactory = $addressFactory;
        $this->currencyModelFactory = $currencyModelFactory;
        $this->localeCurrency = $localeCurrency;
        $this->accountManager = $accountManager;
        $this->_quoteFactory = $quoteFactory;
        $this->_quickPayOrdersFactory = $quickPayOrdersFactory;
    }

    /**
     * Return store.
     *
     * @return object
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Return store.
     *
     * @param int $storeId
     *
     * @return object
     */
    public function getStoreById($storeId)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Return string.
     *
     * @param string $routePath
     * @param array $routeParams
     *
     * @return string
     */
    public function getQuickPayUrl($routePath, $routeParams)
    {
        return $this->_urlHelper->getUrl($routePath, $routeParams);
    }

    /**
     * Format Price
     *
     * @param float $price
     * @param string|null $quoteCurrency
     *
     * @return string
     */
    public function getFormatedPrice($price, $quoteCurrency = '')
    {
        if ($quoteCurrency) {
            $currencySymbol = $this->localeCurrency->getCurrency($quoteCurrency)->getSymbol();
            return $this->currencyModelFactory->create()->format(
                $price,
                ['symbol' => $currencySymbol, 'precision'=> 2],
                false,
                false
            );
        } else {
            return $this->priceHelper->currency($price, true, false);
        }
    }

    public function convertPrice($price, $currencyCodeFrom, $currencyCodeTo)
    {
        $rate = $this->currencyModelFactory->create()
            ->load($currencyCodeFrom)
            ->getAnyRate($currencyCodeTo);
        
        $convertedPrice = $price * $rate;

        return $convertedPrice;
    }

    /**
     * send email
     *
     * @param object $quote
     * @param int $quickPayId
     *
     * @return null
     */
    public function sendQuickPayEmail($quote, $quickPayId)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $email_template = $this->scopeConfig->getValue('quick_pay_for_order/email/template', $storeScope);
        $senderName = $this->scopeConfig->getValue('trans_email/ident_sales/name', $storeScope);
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_sales/email', $storeScope);

        $templateVars = [];
        $templateHtml = $this->createTemplate($quote, $quickPayId);
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $quote->getStore()->getId()
        ];
        
        $templateVars = [
            'store' => $quote->getStore(),
            'items' => $templateHtml['items'],
            'quickPayLink' => $templateHtml['quickPayLink'],
            'downloadLink' => $templateHtml['downloadLink'],
            'shippingCharge' => $templateHtml['shippingCharge'],
            'discountAmount' => $templateHtml['discountAmount'],
            'taxAmount' => $templateHtml['taxAmount'],
            'giftWrapperAmount' => $templateHtml['giftWrapperAmount'],
            'grandTotal' => $templateHtml['grandTotal']
        ];
        if ($senderName == '') {
            $senderName = "Sales";
        }
        if ($senderEmail == '') {
            $senderEmail = "sales@example.com";
        }
        $from = ['email' => $senderEmail, 'name' => $senderName];
        $to = ['email' => $quote->getCustomerEmail(), 'name' => $quote->getCustomerFirstname()];
        $this->_inlineTranslation->suspend();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($email_template)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($from)
            ->addTo($to['email'], $to['name'])
            ->getTransport();
        $transport->sendMessage();
        $this->_inlineTranslation->resume();
    }

    /**
     * Return array.
     *
     * @return array
     */
    public function createTemplate($quote, $quickPayId)
    {
        $templateVars = [];
        $items = $quote->getAllVisibleItems();
        $itemsHtml = '';
        $serviceProductId = $this->getServiceProductId();
        $quoteCurrency = $quote->getCurrency()->getData('quote_currency_code');
        foreach ($items as $item) {
            $price = 0;
            if ($item->getSku() == 'wk_service') {
                $price = $item->getCustomPrice();
            } else {
                $quoteRate = $quote->getData('base_to_quote_rate');
                $price = $item->getPrice()*$quoteRate;
            }
            $serviceHtml = '';
            if ($item->getProductId() == $serviceProductId && $item->getSku() == self::SKU) {
                $optionData = $item->getOptionByCode('additional_options');
                $additionalOptions = [];
                if ($optionData) {
                    $additionalOptions = json_decode($optionData->getValue(), true);
                }
                if (!empty($additionalOptions)) {
                    $serviceHtml .= '<table>';
                    $serviceHtml .= '<tr><th>'.__('Name').'</th><th>'.__('Price').'</th></tr>';
                    foreach ($additionalOptions as $option) {
                        $serviceHtml .= '<tr><td>'.$option['label'].'</td>';
                        $serviceHtml .= '<td>'.$this->getFormatedPrice($option['value'], $quoteCurrency).'</td></tr>';
                    }
                    $serviceHtml .= '</table>';
                }
            }
            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td class="item-info">'.$item->getName().'<br>'.$serviceHtml.'</td>';
            $itemsHtml .= '<td class="item-info">'.$item->getSku().'</td>';
            $itemsHtml .= '<td class="item-qty">'.$item->getQty().'</td>';
            $itemsHtml .= '<td class="item-price">';
            $itemsHtml .= $this->getFormatedPrice($item->getQty()*$price, $quoteCurrency).'</td>';
            $itemsHtml .= '</tr>';
        }

        $shippingAmount = $this->getFormatedPrice(
            $quote->getShippingAmount(),
            $quoteCurrency
        );
        $discountAmount = $this->getFormatedPrice(
            $quote->getSubtotal() - $quote->getSubtotalWithDiscount(),
            $quoteCurrency
        );
        $taxAmount = $this->getFormatedPrice(
            $quote->getShippingAddress()->getData('tax_amount'),
            $quoteCurrency
        );

        $giftWrapperAmount = 0;
        if ($quote->getShippingAddress()->getData('gw_price')) {
            $giftWrapperAmount = $this->getFormatedPrice(
                $quote->getShippingAddress()->getData('gw_price'),
                $quoteCurrency
            );
        }
        
        $grandTotal = $this->getFormatedPrice(
            $quote->getGrandTotal(),
            $quoteCurrency
        );

        $url = $this->getQuickPayUrl(
            'quickpayfororder/order/view',
            [
                'id' => base64_encode($quote->getId()),
                'payid' => base64_encode($quickPayId),
                '_nosid' => true
            ]
        );

        $downloadLink = '';
        $payOrder = $this->getQuickPayOrderQuote($quickPayId);
        if ($payOrder->getAttachment()) {
            $downloadLink = $this->getQuickPayUrl(
                'quickpayfororder/order/download',
                ['id' => base64_encode($quickPayId)]
            );
        }

        $templateVars['quickPayLink'] = $url;
        $templateVars['downloadLink'] = $downloadLink;
        $templateVars['items'] = $itemsHtml;
        $templateVars['shippingCharge'] = $shippingAmount;
        $templateVars['discountAmount'] = $discountAmount;
        $templateVars['taxAmount'] = $taxAmount;
        $templateVars['giftWrapperAmount'] = $giftWrapperAmount;
        $templateVars['grandTotal'] = $grandTotal;

        return $templateVars;
    }

    public function getQuickPayOrderQuote($quickPayId)
    {
        return $this->_quickPayOrdersFactory->create()->load($quickPayId);
    }
    
    /**
     * Create new customer and return customer object.
     *
     * @return object
     */
    public function createCustomer($quote, $store)
    {
        $customer = $this->customerFactory->create();
        $shipAddress = $quote->getShippingAddress();
        $billAddress = $quote->getBillingAddress();

        $firstName = ($shipAddress->getFirstname()) ? $shipAddress->getFirstname() : $billAddress->getFirstname();
        $lastName = ($shipAddress->getLastname()) ? $shipAddress->getLastname() : $billAddress->getLastname();
        $email = ($shipAddress->getEmail()) ? $shipAddress->getEmail() : $billAddress->getEmail();
        $countryId = ($shipAddress->getCountryId()) ? $shipAddress->getCountryId() : $billAddress->getCountryId();
        $city = ($shipAddress->getCity()) ? $shipAddress->getCity() : $billAddress->getCity();
        $postcode = ($shipAddress->getPostcode()) ? $shipAddress->getPostcode() : $billAddress->getPostcode();
        $telephone = ($shipAddress->getTelephone()) ? $shipAddress->getTelephone() : $billAddress->getTelephone();
        $street = ($shipAddress->getData('street')) ? $shipAddress->getData('street') : $billAddress->getData('street');
        $region = ($shipAddress->getRegion()) ? $shipAddress->getRegion() : $billAddress->getRegion();
        $regionId = ($shipAddress->getRegionId()) ? $shipAddress->getRegionId() : $billAddress->getRegionId();

        $customer->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setEmail($email)
            ->setGroupId(1)
            ->setCreatedAt(null);

        $customer = $this->accountManager->createAccount($customer, $password = null, $redirectUrl = '');

        $address = $this->addressFactory->create();
        $address->setCustomerId($customer->getId())
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setCountryId($countryId)
            ->setPostcode($postcode)
            ->setCity($city)
            ->setRegion($region)
            ->setRegionId($regionId)
            ->setTelephone($telephone)
            ->setFax('')
            ->setCompany('')
            ->setStreet($street)
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1')
            ->save();

        return $customer;
    }

    /**
     * return customer
     *
     * @return object
     */
    public function getCustomer($customer)
    {
        return $this->getCustomerById($customer->getId());
    }

    /**
     * return customer
     *
     * @return object
     */
    public function getCustomerById($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        return $customer;
    }

    /**
     * return service product id
     *
     * @return int
     */
    public function getServiceProductId()
    {
        $productId = null;
        try {
            $product = $this->productRepository->get(self::SKU);
            $productId = $product->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $productId = null;
        }

        return $productId;
    }

    /**
     * Return quote object.
     *
     * @return null|object \Magento\Quote\Model\Quote
     */
    public function getQuoteById($quoteId)
    {
        if ($quoteId != '') {
            $quote = $this->_quoteFactory->create()->loadByIdWithoutStore($quoteId);
            return $quote;
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote;
     */
    public function validateQuote($quote): bool
    {
        $status = false;
        foreach ($quote->getAllItems() as $item) {
            if ($item->getSku() == self::SKU) {
                $status = true;
                break;
            }

            if ($item->getCustomPrice()) {
                $status = true;
                break;
            }
        }

        return $status;
    }
}
