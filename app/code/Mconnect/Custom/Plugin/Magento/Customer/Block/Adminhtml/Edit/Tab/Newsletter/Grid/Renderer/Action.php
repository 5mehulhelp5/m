<?php
declare(strict_types=1);

namespace Mconnect\Custom\Plugin\Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer;

class Action
{

    protected $_coreRegistry;
    
    public function __construct(
        
        \Magento\Framework\Registry $registry
        
    ) {
        $this->_coreRegistry = $registry;
        
        
    } 

    public function aroundRender(
        \Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Renderer\Action $subject,
        \Closure $proceed,
        $row
    ) {
        //Your plugin code
        $actions = [];

        $actions[] = [
            '@' => [
                'href' => $subject->getUrl(
                    'newsletter/template/preview',
                    [
                        'id' => $row->getTemplateId(),
                     //   'subscriber' => $this->_coreRegistry->registry('subscriber')->getId()
                        'subscriber' => $this->_coreRegistry->registry('current_customer_id')
                    ]
                ),
                'target' => '_blank',
            ],
            '#' => __('View'),
        ];

        return $this->_actionsToHtml($actions);


        $result = $proceed($row);
        return $result;
    }


    protected function _actionsToHtml(array $actions)
    {
        $html = [];
        $attributesObject = new \Magento\Framework\DataObject();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }
}