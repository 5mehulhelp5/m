<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Plugin\Klarna\Chekout\Model\Api\Rest\Service;

use Klarna\Kco\Model\Api\Rest\Service\Checkout;

class UpdateOrderLine
{
    public function beforeUpdateOrder(Checkout $subject, string $id, array $data, ?string $currency = null): array
    {
        if (isset($data['order_lines'])) {
            foreach ($data['order_lines'] as $key => $item) {
                if ($item['reference'] === 'amgiftcard') {
                    $data['order_amount'] += $item['total_amount'];
                }
            }
        }

        return [$id, $data, $currency];
    }
}
