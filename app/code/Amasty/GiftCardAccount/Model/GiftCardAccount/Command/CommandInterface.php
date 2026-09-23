<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */
namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Command;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

interface CommandInterface
{
    /**
     * @return GiftCardAccountInterface
     */
    public function execute(): GiftCardAccountInterface;
}
