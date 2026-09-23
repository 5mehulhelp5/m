<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\ViewModel\System\Config;

use Amasty\StoreCredit\Model\Source\RestrictAction;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Note implements ArgumentInterface
{
    public const SKU_FIELD_PATH = 'amstorecredit_usage_skus';
    public const CATEGORY_FIELD_PATH = 'amstorecredit_usage_category_ids';

    /**
     * @var array
     */
    private $noteMap = [];

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    public function __construct(JsonSerializer $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->_construct();
    }

    private function _construct(): void
    {
        $this->noteMap[RestrictAction::INCLUDE][self::SKU_FIELD_PATH]
            = __('Specify a comma-separated list of SKUs the store credit can be applied to.');
        $this->noteMap[RestrictAction::EXCLUDE][self::SKU_FIELD_PATH]
            = __('Specify a comma-separated list of SKUs the store credit can\'t be applied to.');
        $this->noteMap[RestrictAction::INCLUDE][self::CATEGORY_FIELD_PATH]
            = __('Specify a comma-separated list of category IDs the store credit can be applied to.');
        $this->noteMap[RestrictAction::EXCLUDE][self::CATEGORY_FIELD_PATH]
            = __('Specify a comma-separated list of category IDs the store credit can\'t be applied to.');
    }

    /**
     * @return string
     */
    public function getMap(): string
    {
        return $this->jsonSerializer->serialize($this->noteMap);
    }
}
