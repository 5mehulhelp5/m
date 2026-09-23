<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model;

use Amasty\StoreCredit\Model\Config\Utils;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var string
     */
    protected $pathPrefix = 'amstorecredit/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const XPATH_ENABLED = 'general/enabled';
    public const REFUND_AUTOMATICALLY = 'general/refund_automatically';
    public const ALLOW_ON_TAX = 'general/allow_on_tax';
    public const ALLOW_ON_SHIPPING = 'general/allow_on_shipping';
    public const TRANSACTIONS_LOG_CLEANING = 'general/transactions_log_cleaning';
    public const TRANSACTIONS_LOG_PERIOD = 'general/transactions_log_period';

    public const RESTRICT_PRODUCTS = 'usage/restrict';
    public const RESTRICT_ACTION = 'usage/action';
    public const PRODUCT_SKUS = 'usage/skus';
    public const CATEGORY_IDS = 'usage/category_ids';
    public const TOOLTIP_ENABLED = 'usage/use_tooltip';
    public const TOOLTIP_TEXT = 'usage/tooltip_text';
    public const ENCOURAGE = 'usage/encourage';

    public const EMAIL_ENABLED = 'email/enabled';
    public const EMAIL_ACTIONS = 'email/actions';
    public const EMAIL_SENDER = 'email/sender';
    public const EMAIL_REPLY = 'email/reply';
    public const EMAIL_TEMPLATE = 'email/template';
    /**#@-*/

    public function __construct(Utils $utils, ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($scopeConfig);
        $this->utils = $utils;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XPATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isRefundAutomatically()
    {
        return $this->isSetFlag(self::REFUND_AUTOMATICALLY);
    }

    /**
     * @var int $storeId
     *
     * @return bool
     */
    public function isAllowOnTax($storeId = null)
    {
        return $this->isSetFlag(self::ALLOW_ON_TAX, $storeId);
    }

    /**
     * @var int $storeId
     *
     * @return bool
     */
    public function isAllowOnShipping($storeId = null)
    {
        return $this->isSetFlag(self::ALLOW_ON_SHIPPING, $storeId);
    }

    public function isEnableTransactionsLogCleaning(
        ?int $storeId = null,
        string $scope = ScopeInterface::SCOPE_STORE
    ): bool {
        return $this->isSetFlag(self::TRANSACTIONS_LOG_CLEANING, $storeId, $scope);
    }

    public function getTransactionsLogPeriod(?int $storeId = null, string $scope = ScopeInterface::SCOPE_STORE): int
    {
        return (int)$this->getValue(self::TRANSACTIONS_LOG_PERIOD, $storeId, $scope);
    }

    /**
     * @return bool
     */
    public function isEmailEnabled()
    {
        return $this->isSetFlag(self::EMAIL_ENABLED);
    }

    /**
     * @return array
     */
    public function getEmailActions()
    {
        return explode(',', $this->getValue(self::EMAIL_ACTIONS));
    }

    /**
     * @return string
     */
    public function getEmailSender()
    {
        return $this->getValue(self::EMAIL_SENDER);
    }

    /**
     * @return string
     */
    public function getEmailReplyTo()
    {
        return $this->getValue(self::EMAIL_REPLY);
    }

    /**
     * @return string
     */
    public function getEmailTemplate($storeId = 0)
    {
        return $this->getValue(self::EMAIL_TEMPLATE, $storeId);
    }

    public function getConfigValue(...$args)
    {
        return $this->getValue(...$args);
    }

    public function getGlobalConfigValue($path)
    {
        $this->scopeConfig->getValue($this->pathPrefix . $path);
    }

    /**
     * @return bool
     */
    public function isRestrictProductsEnabled(): bool
    {
        return $this->isSetFlag(self::RESTRICT_PRODUCTS);
    }

    /**
     * @return int
     */
    public function getRestrictAction(): int
    {
        return (int) $this->getValue(self::RESTRICT_ACTION);
    }

    /**
     * @return array
     */
    public function getProductSkusForRestrict(): array
    {
        return $this->utils->convertToArray((string) $this->getValue(self::PRODUCT_SKUS));
    }

    /**
     * @return array
     */
    public function getCategoryIdsForRestrict(): array
    {
        return $this->utils->convertToArray((string) $this->getValue(self::CATEGORY_IDS));
    }

    /**
     * @return bool
     */
    public function isTooltipEnabled(): bool
    {
        return $this->isSetFlag(self::TOOLTIP_ENABLED);
    }

    /**
     * @return string
     */
    public function getTooltipText(): string
    {
        return (string) $this->getValue(self::TOOLTIP_TEXT);
    }

    /**
     * @return bool
     */
    public function isEncourage(): bool
    {
        return $this->isSetFlag(self::ENCOURAGE);
    }
}
