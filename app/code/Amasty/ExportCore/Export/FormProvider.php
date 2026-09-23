<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;

class FormProvider
{
    /**
     * @var \Amasty\ExportCore\Api\FormInterface[]
     */
    private $compositeForm;

    public function __construct(
        array $compositeForm
    ) {
        //TODO check
        $this->compositeForm = $compositeForm;
    }

    public function get(string $compositeFormType)
    {
        if (!isset($this->compositeForm[$compositeFormType])) {
            throw new \RuntimeException('No meta');
        }

        return $this->compositeForm[$compositeFormType];
    }
}
