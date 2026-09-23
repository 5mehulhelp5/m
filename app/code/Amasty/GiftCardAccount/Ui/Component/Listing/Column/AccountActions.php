<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Ui\Component\Listing\Column;

use Amasty\GiftCard\Model\Image\Utils\AdditionalExtensionsChecker;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class AccountActions extends Column
{
    private const DOWNLOAD_ACTION_NAME = 'download';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var AdditionalExtensionsChecker
     */
    private $additionalExtensionsChecker;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AdditionalExtensionsChecker $additionalExtensionsChecker,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->additionalExtensionsChecker = $additionalExtensionsChecker;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[GiftCardAccountInterface::ACCOUNT_ID])) {
                    $this->processItem($item);
                }
            }
        }

        return $dataSource;
    }

    private function processItem(array &$item): void
    {
        $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'id';
        $config = (array)$this->getData('config');
        if ($config && isset($config['buttons'])) {
            foreach ($config['buttons'] as $actionName => $button) {
                if ($actionName === self::DOWNLOAD_ACTION_NAME && !$this->isDownloadActionAvailable()) {
                    continue;
                }
                $item[$this->getData('name')][$actionName] = $this->prepareButtonConfig(
                    $button,
                    $urlEntityParamName,
                    (int)$item[GiftCardAccountInterface::ACCOUNT_ID]
                );
            }
        }
    }

    private function isDownloadActionAvailable(): bool
    {
        return $this->additionalExtensionsChecker->isImagickEnabled();
    }

    private function prepareButtonConfig(array $button, string $urlEntityParamName, int $accountId): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                $button['urlPath'],
                [
                    $urlEntityParamName => $accountId
                ]
            ),
            'label' => $button['itemLabel']
        ];
    }
}
