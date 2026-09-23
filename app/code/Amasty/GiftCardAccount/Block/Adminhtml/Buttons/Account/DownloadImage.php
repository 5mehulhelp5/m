<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Block\Adminhtml\Buttons\Account;

use Amasty\GiftCard\Model\Image\Utils\AdditionalExtensionsChecker;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DownloadImage implements ButtonProviderInterface
{
    /**
     * @var AdditionalExtensionsChecker
     */
    private $additionalExtensionsChecker;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        AdditionalExtensionsChecker $additionalExtensionsChecker,
        RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->additionalExtensionsChecker = $additionalExtensionsChecker;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    public function getButtonData(): array
    {
        $id = (int)$this->request->getParam(GiftCardAccountInterface::ACCOUNT_ID);

        if (!$this->additionalExtensionsChecker->isImagickEnabled() || !$id) {
            return [];
        }
        $onClick = sprintf('window.location.assign("%s")', $this->getDownloadUrl($id));

        return [
            'label' => __('Download Image'),
            'class' => 'download',
            'on_click' => $onClick,
            'sort_order' => 0
        ];
    }

    private function getDownloadUrl(int $id): string
    {
        return $this->urlBuilder->getUrl('*/*/download', [GiftCardAccountInterface::ACCOUNT_ID => $id]);
    }
}
