<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\History\Repository;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Magento\Backend\Model\Auth\Session;

class GetHistoryAdminName implements GetHistoryAdminNameInterface
{
    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var int[]
     */
    private $adminActions;

    /**
     * @param Session $authSession
     * @param int[] $adminActions
     */
    public function __construct(Session $authSession, array $adminActions = [])
    {
        $this->authSession = $authSession;
        $this->adminActions = $adminActions;
    }

    public function execute(HistoryInterface $history): ?string
    {
        return in_array($history->getAction(), $this->adminActions) ?
            $this->authSession->getUser()->getUserName() :
            null;
    }
}
