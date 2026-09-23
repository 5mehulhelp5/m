<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Select;

use Amasty\ExportCore\Api\Config\Entity\Field\FieldInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterMetaInterface;
use Amasty\ImportExportCore\Utils\OptionsProcessor;
use Magento\Framework\Data\OptionSourceInterface;

class Meta implements FilterMetaInterface
{
    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var array
     */
    private $config;

    /**
     * @var OptionsProcessor
     */
    private $optionsProcessor;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        OptionsProcessor $optionsProcessor,
        $config = []
    ) {
        $this->configFactory = $configFactory;
        $this->optionsProcessor = $optionsProcessor;
        $this->config = $config;
    }

    public function getJsConfig(FieldInterface $field): array
    {
        $options = [];
        if (!empty($this->config['options'])) {
            $options = $this->config['options'];
        } elseif (!empty($this->config['class']) && is_object($this->config['class'])
            && is_subclass_of($this->config['class'], OptionSourceInterface::class)
        ) {
            $options = $this->config['class']->toOptionArray();
        }
        if (empty($options)) {
            return [];
        }

        return [
            'component' => 'Magento_Ui/js/form/element/multiselect',
            'template' => 'ui/form/element/multiselect',
            'options' => $this->optionsProcessor->process($options)
        ];
    }

    private function isMultiselect()
    {
        return !empty($this->config['dataType']) && $this->config['dataType'] == 'multiselect';
    }

    public function getConditions(FieldInterface $field): array
    {
        return [
            ['label' => __('is'), 'value' => $this->isMultiselect() ? 'finset' : 'in'],
            ['label' => __('is not'), 'value' => $this->isMultiselect() ? 'nfinset' : 'nin'],
            ['label' => __('is null'), 'value' => 'null'],
            ['label' => __('is not null'), 'value' => 'notnull'],
        ];
    }

    public function prepareConfig(FieldFilterInterface $filter, $value): FilterMetaInterface
    {
        $config = $this->configFactory->create();
        $config->setValue($value);
        $config->setIsMultiselect($this->isMultiselect());
        $filter->getExtensionAttributes()->setSelectFilter($config);

        return $this;
    }

    public function getValue(FieldFilterInterface $filter)
    {
        if ($config = $filter->getExtensionAttributes()->getSelectFilter()) {
            return $filter->getExtensionAttributes()->getSelectFilter()->getValue();
        }

        return null;
    }
}
