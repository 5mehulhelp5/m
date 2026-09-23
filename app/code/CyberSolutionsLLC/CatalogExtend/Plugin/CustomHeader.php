<?php

namespace CyberSolutionsLLC\CatalogExtend\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class CustomHeader
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    )
    {
        $this->request = $request;
    }

    /**
     * @param ResultInterface $subject
     * @param $result
     * @param ResponseInterface $response
     */
    public function afterRenderResult(ResultInterface $subject, $result, ResponseInterface $response)
    {
        $actionName = $this->request->getFullActionName();
        if ($actionName === 'catalogsearch_result_index') {
            $response->setHeader('X-Robots-Tag', 'NOINDEX, FOLLOW');
        } elseif ($this->request->isXmlHttpRequest()) {
            $response->setHeader('X-Robots-Tag', 'NOINDEX, NOFOLLOW');
        }
    }
}
