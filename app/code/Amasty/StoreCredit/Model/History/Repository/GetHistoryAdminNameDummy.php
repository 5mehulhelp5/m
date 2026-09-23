<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\History\Repository;

use Amasty\StoreCredit\Api\Data\HistoryInterface;

class GetHistoryAdminNameDummy implements GetHistoryAdminNameInterface
{
    public function execute(HistoryInterface $history): ?string
    {
        return null;
    }
}
