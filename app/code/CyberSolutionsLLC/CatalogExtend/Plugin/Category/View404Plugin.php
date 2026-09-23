<?php

namespace CyberSolutionsLLC\CatalogExtend\Plugin\Category;

use Magento\Catalog\Controller\Category\View;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Response\Http;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;

class View404Plugin
{
    private RequestInterface $request;
    private Http $response;
    private LayerResolver $layerResolver;
    protected FilterableAttributeList $filterAbleAttributeList;

    public function __construct(
        RequestInterface $request,
        Http $response,
        LayerResolver $layerResolver,
        FilterableAttributeList $filterAbleAttributeList
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->layerResolver = $layerResolver;
        $this->filterAbleAttributeList = $filterAbleAttributeList;
    }

    public function afterExecute(View $subject, ResultInterface $result)
    {
        if (!$this->hasAppliedFilters()) {
            return $result;
        }

        $layer = clone $this->layerResolver->get();
        $collection = $layer->getProductCollection();

        if ((int) $collection->getSize() === 0) {
            $this->response->setStatusHeader(404, '1.1', 'Not Found');
        }

        return $result;
    }

    private function hasAppliedFilters(): bool
    {
        $params = $this->request->getParams();

        unset(
            $params['id'],
            $params['p'],
            $params['product_list_order'],
            $params['product_list_dir'],
            $params['product_list_limit']
        );

        if (count($params)) {
            $filterList = $this->filterAbleAttributeList->getList();
            foreach (array_keys($params) as $paramCode) {
                if (($paramCode === 'cat')
                    || $filterList->getItemByColumnValue('attribute_code', $paramCode)
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}
