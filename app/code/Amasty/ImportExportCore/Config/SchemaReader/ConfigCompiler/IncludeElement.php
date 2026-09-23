<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Config\SchemaReader\ConfigCompiler;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Dir;

class IncludeElement extends \Magento\Config\Model\Config\Compiler\IncludeElement
{
    protected function getContent($includePath)
    {
        list($moduleName, $filename) = explode('::', $includePath);

        $directoryRead = $this->readFactory->create(
            $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, $moduleName)
        );

        if ($directoryRead->isExist($filename) && $directoryRead->isFile($filename)) {
            return $directoryRead->readFile($filename);
        }

        throw new LocalizedException(__('The file "%1" does not exist', $filename));
    }
}
