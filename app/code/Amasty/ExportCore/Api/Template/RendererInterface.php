<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\Template;

use Amasty\ExportCore\Api\ChunkStorageInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;

interface RendererInterface
{
    public function render(
        ExportProcessInterface $exportProcess,
        ChunkStorageInterface $chunkStorage
    ): RendererInterface;

    public function getFileExtension(ExportProcessInterface $exportProcess): ?string;
}
