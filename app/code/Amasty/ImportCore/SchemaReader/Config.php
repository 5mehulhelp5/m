<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\SchemaReader;

use Amasty\ImportCore\SchemaReader\Config\Reader;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;

class Config extends Data
{
    public const CACHE_ID = 'amasty_import';

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
