<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class TransactionsDemo implements ArgumentInterface
{
    public function getSubscribeUrl(): string
    {
        return 'https://amasty.com/amcustomer/account/products/'
            . '?utm_source=extension&utm_medium=backend&utm_campaign=subscribe_storecredit';
    }
}
