<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\SchemaReader;

use Amasty\ExportCore\SchemaReader\Config\Reader;
use Magento\Framework\Config\CacheInterface;

class Config extends \Magento\Framework\Config\Data
{
    public const CACHE_ID = 'amasty_export';

    /**
     * Initialize reader and cache.
     *
     * @param Reader $reader
     * @param CacheInterface $cache
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, self::CACHE_ID);
    }
}
