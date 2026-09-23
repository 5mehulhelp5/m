<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Block\Adminhtml\Customer\Edit\Renderer;

use Amasty\StoreCredit\Api\Data\HistoryInterface;
use Amasty\StoreCredit\Model\History\MessageProcessor;
use Magento\Backend\Block\Context;
use Magento\Framework\App\ObjectManager;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    public function render(\Magento\Framework\DataObject $row)
    {
        //use object manager to avoid loading dependencies of parent class
        $objectManager = ObjectManager::getInstance();
        $messageProcessor = $objectManager->create(MessageProcessor::class);
        return $messageProcessor->processSmall(
            $row->getData(HistoryInterface::ACTION),
            [
                array_merge(
                    [
                        $row->getData(HistoryInterface::DIFFERENCE),
                        $row->getData(HistoryInterface::STORE_CREDIT_BALANCE)
                    ],
                    json_decode($row->getData(HistoryInterface::ACTION_DATA), true)
                )
            ]
        );
    }
}
