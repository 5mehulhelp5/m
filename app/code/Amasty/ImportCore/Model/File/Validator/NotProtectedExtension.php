<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Model\File\Validator;

use Amasty\ImportCore\Api\Source\SourceConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension as MagentoNotProtectedExtension;

/**
 * In M2.4+ XML type was added to validator as protected
 * must remove it for import file upload
 */
class NotProtectedExtension extends MagentoNotProtectedExtension
{
    /**
     * @var SourceConfigInterface
     */
    private $sourceConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SourceConfigInterface $sourceConfig
    ) {
        $this->sourceConfig = $sourceConfig;
        parent::__construct($scopeConfig);
    }

    public function getProtectedFileExtensions($store = null)
    {
        $extensions = parent::getProtectedFileExtensions();
        if (is_string($extensions)) {
            $extensions = explode(',', $extensions);
        }

        return $this->unsetImportFileTypes($extensions);
    }

    private function unsetImportFileTypes(array $extensions): array
    {
        $sources = $this->sourceConfig->all();

        foreach ($extensions as $key => $extension) {
            if (isset($sources[$extension])) {
                unset($extensions[$key]);
            }
        }

        return $extensions;
    }
}
