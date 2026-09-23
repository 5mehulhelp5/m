<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Custom\Controller\Adminhtml\Product\Initialization;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class StockDataFilter
 */
class StockDataFilter extends \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter
{
    /**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    

    /**
     * Filter stock data
     *
     * @param array $stockData
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function filter(array $stockData)
    {
        
        /** Fix for the decimal values error we got while updating the stocks */
        if(array_key_exists('is_qty_decimal',$stockData)){
            if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
                $stockData['is_decimal_divided'] = 0;
            }
        }else{
            if (!isset($stockData['is_decimal_divided'])) {
                $stockData['is_decimal_divided'] = 0;
            }
        }
        return $stockData;
    }
}
