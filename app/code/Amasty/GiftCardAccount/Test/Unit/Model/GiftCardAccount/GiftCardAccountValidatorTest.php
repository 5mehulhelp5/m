<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Test\Unit\Model\GiftCardAccount;

use Amasty\GiftCard\Model\CodePool\CodePoolRule;
use Amasty\GiftCard\Model\CodePool\Repository;
use Amasty\GiftCard\Model\ConfigProvider as CardConfigProvider;
use Amasty\GiftCardAccount\Model\ConfigProvider as AccountConfigProvider;
use Amasty\GiftCardAccount\Model\CustomerCard\Repository as CustomerCardRepository;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Account;
use Amasty\GiftCardAccount\Model\GiftCardAccount\GiftCardAccountValidator;
use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\AllowedTotalCalculator;
use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\Quote as GiftCardQuote;
use Amasty\GiftCardAccount\Model\OptionSource\AccountStatus;
use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\Data\CartExtension;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManager;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Amasty\GiftCardAccount\Model\GiftCardAccount\GiftCardAccountValidator
 */
class GiftCardAccountValidatorTest extends \PHPUnit\Framework\TestCase
{
    public const WEBSITE_ID = 0;
    public const STORE_ID = 1;
    public const CODE = 'test_code';
    public const CODE_POOL_ID = 1;
    public const ALLOWED_SUBTOTAL = 50;
    public const CUSTOMER_ID = 1;
    public const CARD_LIMIT = 1;

    /**
     * @var GiftCardAccountValidator
     */
    private $accountValidator;

    /**
     * @var AllowedTotalCalculator|MockObject
     */
    private $totalCalculator;

    /**
     * @var Session|MockObject
     */
    private $session;

    /**
     * @var CardConfigProvider|MockObject
     */
    private $cardConfig;

    /**
     * @var AccountConfigProvider|MockObject
     */
    private $accountConfig;

    /**
     * @var Repository|MockObject
     */
    private $codePoolRepository;

    /**
     * @var CustomerCardRepository
     */
    private $customerCardRepository;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->totalCalculator = $this->createPartialMock(AllowedTotalCalculator::class, ['getAllowedSubtotal']);
        $this->session = $this->createPartialMock(Session::class, ['isLoggedIn', 'getCustomerId']);
        $this->session->expects($this->any())->method('getCustomerId')->willReturn(self::CUSTOMER_ID);
        $this->cardConfig = $this->createPartialMock(
            CardConfigProvider::class,
            ['isAllowUseThemselves', 'getAllowedProductTypes', 'isEnabled', 'isAllowAssignToCustomer']
        );
        $this->accountConfig = $this->createPartialMock(
            AccountConfigProvider::class,
            ['getMaxGiftCardQty', 'isMultipleWebsitesAllowed']
        );
        $this->codePoolRepository = $this->createPartialMock(Repository::class, ['getRuleByCodePoolId']);
        $this->customerCardRepository = $this->createPartialMock(
            CustomerCardRepository::class,
            ['getByAccountAndCustomerId']
        );

        $this->accountValidator = $objectManager->getObject(
            GiftCardAccountValidator::class,
            [
                'storeManager' => $this->getStoreManager(),
                'allowedTotalCalculator' => $this->totalCalculator,
                'cardConfigProvider' => $this->cardConfig,
                'accountConfigProvider' => $this->accountConfig,
                'customerSession' => $this->session,
                'codePoolRepository' => $this->codePoolRepository,
                'customerCardRepository' => $this->customerCardRepository
            ]
        );
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(
        $accountId,
        $websiteId,
        $status,
        $value,
        $errorMessage,
        $isAllowAssignToCustomer,
        $isMultipleWebsitesAllowed
    ) {
        $code = $this->createPartialMock(\Amasty\GiftCard\Model\Code\Code::class, []);
        $code->setCode(self::CODE);

        $account = $this->createPartialMock(Account::class, []);
        $account->setCodeModel($code);
        $account->setAccountId($accountId);
        $account->setWebsiteId($websiteId);
        $account->setStatus($status);
        $account->setCurrentValue($value);

        $this->accountConfig
            ->expects($this->any())
            ->method('isMultipleWebsitesAllowed')
            ->willReturn($isMultipleWebsitesAllowed);
        $this->cardConfig
            ->expects($this->any())
            ->method('isAllowAssignToCustomer')
            ->willReturn($isAllowAssignToCustomer);
        if ($isAllowAssignToCustomer) {
            $exceptionMock = new NoSuchEntityException(__('Customer Card not found.'));
            $this->customerCardRepository
                ->expects($this->once())->method('getByAccountAndCustomerId')->willThrowException($exceptionMock);
        }

        $this->expectExceptionMessage($errorMessage);

        $customer = $this->createPartialMock(Customer::class, ['getId']);
        $customer->expects($this->any())->method('getId')->willReturn(self::CUSTOMER_ID);
        $quote = $this->createPartialMock(Quote::class, ['getCustomer']);
        $quote->expects($this->any())->method('getCustomer')->willReturn($customer);

        $this->accountValidator->validate($account, $quote);
    }

    public function testCanApplyForQuoteDiscountReached()
    {
        $account = $this->getValidAccount();

        $quote = $this->createPartialMock(Quote::class, ['getExtensionAttributes']);
        if (method_exists(CartExtension::class, 'getAmGiftcardQuote')) {
            $extensionAttributes = $this->createMock(CartExtension::class);
        } else {
            $extensionAttributes = $this->getMockBuilder(CartExtension::class)
                ->addMethods(['getAmGiftcardQuote',])
                ->getMock();
        }

        $gCardQuote = $this->createPartialMock(GiftCardQuote::class, []);
        $gCardQuote->setGiftAmountUsed(self::ALLOWED_SUBTOTAL);

        $extensionAttributes->expects($this->atLeastOnce())->method('getAmGiftcardQuote')
            ->willReturn($gCardQuote);
        $quote->expects($this->atLeastOnce())->method('getExtensionAttributes')
            ->willReturn($extensionAttributes);
        $this->totalCalculator->expects($this->any())->method('getAllowedSubtotal')
            ->willReturn(self::ALLOWED_SUBTOTAL);

        $this->expectExceptionMessage('Gift card can\'t be applied. Maximum discount reached.');
        $this->accountValidator->canApplyForQuote($account, $quote);
    }

    public function testValidateByCodeQtyLimitReached()
    {
        $store = $this->createPartialMock(\Magento\Store\Model\Store::class, ['getId']);
        $store->expects($this->any())->method('getId')->willReturn(self::STORE_ID);
        $storeManager = $this->createPartialMock(StoreManager::class, ['getStore']);
        $storeManager->expects($this->any())->method('getStore')
            ->willReturn($store);

        $this->accountConfig
            ->expects($this->any())
            ->method('getMaxGiftCardQty')
            ->willReturn(self::CARD_LIMIT);

        $this->expectExceptionMessage('You can\'t apply more than ' . self::CARD_LIMIT . ' gift card code.');

        $this->invokeMethod($this->accountValidator, 'validateByCodeQty', [[
            0 => ['id' => 1, 'code' => 'CODE_1', 'amount' => 15, 'b_amount' => 15]
        ]]);
    }

    public function testCanApplyForQuoteCustomer()
    {
        $account = $this->getValidAccount();
        $account->setCustomerCreatedId(self::CUSTOMER_ID);

        $customer = $this->createPartialMock(Customer::class, ['getId']);
        $customer->expects($this->any())->method('getId')->willReturn(self::CUSTOMER_ID);

        $quote = $this->createPartialMock(Quote::class, ['getExtensionAttributes', 'getCustomer']);
        $quote->expects($this->atLeastOnce())->method('getExtensionAttributes')->willReturn(null);
        $quote->expects($this->any())->method('getCustomer')->willReturn($customer);

        $this->session->expects($this->atLeastOnce())->method('isLoggedIn')->willReturn(true);
        $this->cardConfig->expects($this->once())->method('isAllowUseThemselves')->willReturn(false);

        $this->expectExceptionMessage('Please be aware that it is not possible to use'
            . ' the gift card you purchased for your own orders.');
        $this->accountValidator->canApplyForQuote($account, $quote);
    }

    public function testCanApplyForQuoteValid()
    {
        $account = $this->getValidAccount();

        $customer = $this->createPartialMock(Customer::class, ['getId']);
        $customer->expects($this->any())->method('getId')->willReturn(self::CUSTOMER_ID);

        $quote = $this->createPartialMock(Quote::class, ['getExtensionAttributes', 'getCustomer']);
        $quote->expects($this->atLeastOnce())->method('getExtensionAttributes')->willReturn(null);
        $quote->expects($this->any())->method('getCustomer')->willReturn($customer);

        $this->session->expects($this->atLeastOnce())->method('isLoggedIn')->willReturn(false);

        $this->accountConfig
            ->expects($this->once())
            ->method('isMultipleWebsitesAllowed')
            ->willReturn(true);

        $this->assertTrue($this->accountValidator->canApplyForQuote($account, $quote));
    }

    public function testValidateCode()
    {
        $account = $this->getValidAccount();
        $address = $this->createPartialMock(Address::class, []);
        $quoteItem = $this->createPartialMock(Item::class, ['getAddress']);
        $quoteItem->expects($this->any())->method('getAddress')->willReturn($address);

        $customer = $this->createPartialMock(Customer::class, ['getId']);
        $customer->expects($this->any())->method('getId')->willReturn(self::CUSTOMER_ID);

        $quote = $this->createPartialMock(Quote::class, ['getAllVisibleItems', 'getCustomer']);
        $quote->expects($this->atLeastOnce())->method('getAllVisibleItems')->willReturn([$quoteItem]);
        $quote->expects($this->any())->method('getCustomer')->willReturn($customer);

        $codePoolRule = $this->createPartialMock(CodePoolRule::class, ['getConditions', 'validate']);
        $codePoolRule->expects($this->any())->method('validate')->with($address)->willReturn(true);
        $this->codePoolRepository->expects($this->once())->method('getRuleByCodePoolId')
            ->with(self::CODE_POOL_ID)
            ->willReturn($codePoolRule);

        $this->assertTrue($this->accountValidator->validateCode($account, $quote));
    }

    public function testValidateCodeNoQuoteItems()
    {
        $account = $this->getValidAccount();

        $customer = $this->createPartialMock(Customer::class, ['getId']);
        $customer->expects($this->any())->method('getId')->willReturn(self::CUSTOMER_ID);

        $quote = $this->createPartialMock(Quote::class, ['getAllVisibleItems', 'getCustomer']);
        $quote->expects($this->atLeastOnce())->method('getAllVisibleItems')->willReturn([]);
        $quote->expects($this->any())->method('getCustomer')->willReturn($customer);

        $codePoolRule = $this->createPartialMock(CodePoolRule::class, ['getConditions', 'validate']);
        $this->codePoolRepository->expects($this->once())->method('getRuleByCodePoolId')
            ->with(self::CODE_POOL_ID)
            ->willReturn($codePoolRule);

        $this->assertFalse($this->accountValidator->validateCode($account, $quote));
    }

    public function testValidateCodeNoRule()
    {
        $account = $this->getValidAccount();

        $customer = $this->createPartialMock(Customer::class, ['getId']);
        $customer->expects($this->any())->method('getId')->willReturn(self::CUSTOMER_ID);

        $quote = $this->createPartialMock(Quote::class, ['getCustomer']);
        $quote->expects($this->any())->method('getCustomer')->willReturn($customer);

        $this->codePoolRepository->expects($this->once())->method('getRuleByCodePoolId')
            ->with(self::CODE_POOL_ID)
            ->willReturn(null);

        $this->assertTrue($this->accountValidator->validateCode($account, $quote));
    }

    /**
     * @dataProvider isGiftCardApplicableToCartDataProvider
     */
    public function testIsGiftCardApplicableToCart($enabled, $productType, $expected)
    {
        $quoteItem = $this->createPartialMock(Item::class, ['getProductType', 'getOptions']);
        $quoteItem->expects($this->any())->method('getProductType')->willReturn($productType);
        $quoteItem->expects($this->any())->method('getOptions')->willReturn([]);
        $quote = $this->createPartialMock(Quote::class, ['getAllItems']);
        $quote->expects($this->any())->method('getAllItems')->willReturn([$quoteItem]);

        $this->cardConfig->expects($this->once())->method('isEnabled')->willReturn($enabled);
        $this->cardConfig->expects($this->once())->method('getAllowedProductTypes')->willReturn(
            ['simple']
        );
        $this->assertEquals($expected, $this->accountValidator->isGiftCardApplicableToCart($quote));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [0, self::WEBSITE_ID, 1, 10, 'Wrong gift card code', false, true],
            [1, 2, 1, 10, 'The code cannot be applied on this website.', false, false],
            [
                1,
                self::WEBSITE_ID,
                AccountStatus::STATUS_EXPIRED,
                10,
                'Gift card ' . self::CODE . ' is expired.',
                false,
                true
            ],
            [1, self::WEBSITE_ID, AccountStatus::STATUS_USED, 10, 'Gift card ' . self::CODE . ' is used.', false, true],
            [
                1,
                self::WEBSITE_ID,
                AccountStatus::STATUS_INACTIVE,
                10,
                'Gift card ' . self::CODE . ' is not enabled.',
                false,
                true
            ],
            [
                1,
                self::WEBSITE_ID,
                AccountStatus::STATUS_ACTIVE,
                0,
                'Gift card ' . self::CODE . ' balance does not have funds.',
                false,
                true
            ],
            [
                1,
                self::WEBSITE_ID,
                AccountStatus::STATUS_ACTIVE,
                10,
                'Please add this Gift Card Code to your customer account to proceed.',
                true,
                true
            ]
        ];
    }

    /**
     * @return array
     */
    public function isGiftCardApplicableToCartDataProvider()
    {
        return [
            [false, '', false],//disabled module
            [true, 'simple', true],//valid product
            [true, 'other', false]//invalid product
        ];
    }

    /**
     * @return StoreManager|MockObject
     */
    protected function getStoreManager()
    {
        $website = $this->createPartialMock(\Magento\Store\Model\Website::class, []);
        $website->setId(self::WEBSITE_ID);
        $store = $this->createPartialMock(\Magento\Store\Model\Store::class, ['getId']);
        $store->expects($this->any())->method('getId')->willReturn(self::STORE_ID);
        $storeManager = $this->createPartialMock(StoreManager::class, ['getWebsite', 'getStore']);
        $storeManager->expects($this->any())->method('getWebsite')
            ->willReturn($website);
        $storeManager->expects($this->any())->method('getStore')
            ->willReturn($store);

        return $storeManager;
    }

    /**
     * @return Account|MockObject
     */
    protected function getValidAccount()
    {
        $account = $this->createPartialMock(Account::class, []);
        $account->setAccountId(1);
        $account->setWebsiteId(self::WEBSITE_ID);
        $account->setStatus(AccountStatus::STATUS_ACTIVE);
        $account->setCurrentValue(10);

        $code = $this->createPartialMock(\Amasty\GiftCard\Model\Code\Code::class, []);
        $code->setCode(self::CODE);
        $code->setCodePoolId(self::CODE_POOL_ID);
        $account->setCodeModel($code);

        return $account;
    }

    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
