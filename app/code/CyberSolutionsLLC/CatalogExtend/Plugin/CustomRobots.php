<?php

namespace CyberSolutionsLLC\CatalogExtend\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Page\Config;

class CustomRobots
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $_request;

    /**
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    )
    {
        $this->_request = $request;
    }

    /**
     * @param Config $subject
     * @param string $result
     * @return string
     */
    public function afterGetRobots(Config $subject, string $result): string
    {
        $actionName = $this->_request->getFullActionName();
		/*move to module Robots Extended*/
        /*if ($actionName === 'sendfriend_product_send') {
            return "NOINDEX, NOFOLLOW";
        }
        if ($actionName === 'catalogsearch_result_index') {
            return "NOINDEX, FOLLOW";
        }*/
//		else
	    if ($actionName === 'catalog_category_view') {
		    if ((count($this->_request->getParams()) > 1 && $this->_request->getParam('p') === null)
			    || (count($this->_request->getParams()) > 2 && $this->_request->getParam('p') !== null)) {
			    return "NOINDEX, FOLLOW";
		    }
	    }
        return $result;
    }
}
