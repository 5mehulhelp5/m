<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Temp;

class HelperInitializePlugin
{
    /**
     * @var HelperFactory
     */
    private $helperFactory;

    public function __construct(HelperFactory $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    public function afterInitialize(\Amasty\ExportCore\Api\ExportProcessInterface $subject)
    {
        $subject->getExtensionAttributes()->setHelper($this->helperFactory->create());

        return $subject;
    }
}
