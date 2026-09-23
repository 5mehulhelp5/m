<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Plugin\Quote;

use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\Handlers\ReadHandler;
use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\Handlers\SaveHandler;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Psr\Log\LoggerInterface;

class CartRepositoryPlugin
{
    /**
     * @var ReadHandler
     */
    private $readHandler;

    /**
     * @var SaveHandler
     */
    private $saveHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ReadHandler $readHandler,
        SaveHandler $saveHandler,
        LoggerInterface $logger
    ) {
        $this->readHandler = $readHandler;
        $this->saveHandler = $saveHandler;
        $this->logger = $logger;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function afterGet(CartRepositoryInterface $subject, CartInterface $quote): CartInterface
    {
        $this->readHandler->loadAttributes($quote);

        return $quote;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param SearchResultsInterface $searchResult
     *
     * @return SearchResultsInterface
     */
    public function afterGetList(
        CartRepositoryInterface $subject,
        SearchResultsInterface $searchResult
    ): SearchResultsInterface {
        $quotes = [];

        foreach ($searchResult->getItems() as $quote) {
            $this->readHandler->loadAttributes($quote);
            $quotes[] = $quote;
        }
        $searchResult->setItems($quotes);

        return $searchResult;
    }

    public function afterSave(CartRepositoryInterface $subject, $result, CartInterface $quote): void
    {
        try {
            $this->saveHandler->saveAttributes($quote);
        } catch (LocalizedException $exception) {
            $this->logger->critical($exception);
        }
    }
}
