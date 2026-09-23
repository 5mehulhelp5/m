<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mconnect\Custom\Stdlib\DateTime;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Phrase;

/**
 * Timezone library
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Timezone extends \Magento\Framework\Stdlib\DateTime\Timezone
{
    /**
     * @var array
     */
    
    /**
     * Retrieve date with time
     *
     * @param string $date
     * @param bool $includeTime
     * @return string
     */
    private function appendTimeIfNeeded($date, $includeTime)
    {
        if ($includeTime && !preg_match('/\d{1}:\d{2}/', $date)) {
            $date .= " 0:00";
        }
        return $date;
    }
}
