<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api;

interface ActionInterface
{
    public function initialize(ExportProcessInterface $exportProcess);

    public function execute(ExportProcessInterface $exportProcess);
}
