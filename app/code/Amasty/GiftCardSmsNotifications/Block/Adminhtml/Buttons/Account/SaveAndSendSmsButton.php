<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Gift Card Sms Notifications for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\GiftCardSmsNotifications\Block\Adminhtml\Buttons\Account;

use Amasty\GiftCardSmsNotifications\Model\SmsConfigProvider;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndSendSmsButton implements ButtonProviderInterface
{
    public const ADMIN_RESOURCE = 'Amasty_GiftCardSmsNotifications::send_sms_notifications';

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var SmsConfigProvider
     */
    private $smsConfigProvider;

    public function __construct(
        AuthorizationInterface $authorization,
        SmsConfigProvider $smsConfigProvider
    ) {
        $this->authorization = $authorization;
        $this->smsConfigProvider = $smsConfigProvider;
    }

    public function getButtonData(): array
    {
        if ($this->authorization->isAllowed(self::ADMIN_RESOURCE) && $this->smsConfigProvider->isSmsNotify()) {
            return [
                'label' => __('Save & Send Sms'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'Magento_Ui/js/form/button-adapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'amgcard_account_formedit.areas',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['send_sms' => true, 'back' => 'edit'],
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'on_click' => '',
                'sort_order' => 45
            ];
        }

        return [];
    }
}
