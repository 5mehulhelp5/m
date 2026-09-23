<?php
/**
 * MB "Vienas bitas" (www.magetrend.com)
 *
 * @category  Magetrend Extensions for Magento 2
 * @package  Magetend/NewsletterMaker
 * @author   E. Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-newsletter-maker
 */

namespace Magetrend\NewsletterMaker\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Module helper class
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_GENERAL_IS_ACTIVE = 'newslettermaker/general/is_active';

    const XML_PATH_EMAIL_SENDER_NAME = 'newslettermaker/template_settings/sender_name';

    const XML_PATH_EMAIL_SENDER_EMAIL = 'newslettermaker/template_settings/sender_email';

    public $defaultParser;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magetrend\NewsletterMaker\Model\Parser\DefaultParser $defaultParser
    ) {
        $this->defaultParser = $defaultParser;
        parent::__construct($context);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isActive($store = null)
    {
        if ($this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_IS_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        )) {
            return true;
        }
        return false;
    }

    /**
     * @return \Magetrend\NewsletterMaker\Model\Parser\AbstractParser
     */
    public function getTemplateParser()
    {
        return $this->defaultParser;
    }

    public function getFrom($store = null)
    {
        $senderName = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $senderEmail = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return ['name' => $senderName, 'email' => $senderEmail];
    }
}
