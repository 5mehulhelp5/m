<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Ui\Component\Listing\MassActions;

use Amasty\GiftCard\Model\Image\Utils\AdditionalExtensionsChecker;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Action;

class DownloadAction extends Action
{
    /**
     * @var AdditionalExtensionsChecker
     */
    private $additionalExtensionsChecker;

    public function __construct(
        ContextInterface $context,
        AdditionalExtensionsChecker $additionalExtensionsChecker,
        array $components = [],
        array $data = [],
        $actions = null
    ) {
        parent::__construct($context, $components, $data, $actions);
        $this->additionalExtensionsChecker = $additionalExtensionsChecker;
    }

    public function prepare()
    {
        parent::prepare();

        if (!$this->isActionAvailable()) {
            $config = $this->getConfiguration();
            $config['actionDisable'] = true;
            $this->setData('config', $config);
        }
    }

    private function isActionAvailable(): bool
    {
        return $this->additionalExtensionsChecker->isImagickEnabled()
            && $this->additionalExtensionsChecker->isZipEnabled();
    }
}
