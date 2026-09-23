<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model\GiftCardAccount\CartAction\Response\Builder\AddToCart;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountResponseInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterfaceFactory;
use Amasty\GiftCardAccount\Model\GiftCardAccount\CartAction\Response\Builder\BuilderInterface;
use Amasty\GiftCardProFunctionality\Model\GiftCardAccount\Usage\Checker;
use Magento\Framework\Message\MessageInterface;

class UsageMessage implements BuilderInterface
{
    /**
     * @var GiftCardMessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @var Checker
     */
    private $usageChecker;

    public function __construct(
        GiftCardMessageInterfaceFactory $messageFactory,
        Checker $usageChecker
    ) {
        $this->messageFactory = $messageFactory;
        $this->usageChecker = $usageChecker;
    }

    public function build(
        GiftCardAccountInterface $account,
        GiftCardAccountResponseInterface $response
    ): void {
        if ($this->usageChecker->canBeSetInUsed($account)) {
            $message = $this->messageFactory->create();
            $message->setType(MessageInterface::TYPE_WARNING);
            $message->setText(__('Please mind that you can apply this code only once.')->getText());

            $response->addMessage($message);
        }
    }
}
