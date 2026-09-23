<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\History\Repository;

use Amasty\StoreCredit\Api\Data\HistoryInterface;

interface GetHistoryAdminNameInterface
{
    /**
     * @param HistoryInterface $history
     * @return string|null
     */
    public function execute(HistoryInterface $history): ?string;
}
