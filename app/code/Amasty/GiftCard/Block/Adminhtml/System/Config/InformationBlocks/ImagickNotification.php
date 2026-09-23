<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Block\Adminhtml\System\Config\InformationBlocks;

use Amasty\GiftCard\Model\Image\Utils\AdditionalExtensionsChecker;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

class ImagickNotification extends Template
{
    /**
     * @var AdditionalExtensionsChecker
     */
    private $extensionsChecker;

    public function __construct(
        Template\Context $context,
        AdditionalExtensionsChecker $extensionsChecker,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->extensionsChecker = $extensionsChecker;
    }

    /**
     * @var string
     */
    protected $_template = 'Amasty_GiftCard::config/information/imagick_notification.phtml';

    public function getNotificationText(): Phrase
    {
        $url = 'https://support.amasty.com/portal/en/kb/articles/how-to-fix-a-gift-card-image-not-showing-in-the-email';

        return __(
            'The Imagick extension is required to ensure proper image display across different email clients. '
            . 'If you are unsure how to correctly install the necessary extension on the server, please refer ' . '
            to the details in the <a href="' . $url . '" target="_blank">article</a>.'
        );
    }

    protected function _toHtml()
    {
        if ($this->extensionsChecker->isImagickEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
