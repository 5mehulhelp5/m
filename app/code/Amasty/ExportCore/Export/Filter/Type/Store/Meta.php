<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Filter\Type\Store;

use Amasty\ExportCore\Api\Config\Entity\Field\FieldInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterMetaInterface;
use Amasty\ExportCore\Model\ThirdParty\GwsAdapter;
use Magento\Cms\Ui\Component\Listing\Column\Cms\Options;
use Magento\Framework\App\ObjectManager;

class Meta implements FilterMetaInterface
{
    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var GwsAdapter|null
     */
    private $gwsAdapter;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        Options $options,
        ?GwsAdapter $gwsAdapter = null
    ) {
        $this->configFactory = $configFactory;
        $this->options = $options;
        $this->gwsAdapter = $gwsAdapter ?? ObjectManager::getInstance()->get(GwsAdapter::class);
    }

    public function getJsConfig(FieldInterface $field): array
    {
        $options = $this->getOptions();
        foreach ($options as &$option) {
            $option['value'] = !is_array($option['value'])
                ? (string)$option['value']
                : $option['value'];
        }

        return [
            'component' => 'Magento_Ui/js/form/element/multiselect',
            'template' => 'ui/form/element/multiselect',
            'options' => $options
        ];
    }

    public function getConditions(FieldInterface $field): array
    {
        return [
            ['label' => __('is'), 'value' => 'in'],
            ['label' => __('is not'), 'value' => 'nin'],
            ['label' => __('is null'), 'value' => 'null'],
            ['label' => __('is not null'), 'value' => 'notnull'],
        ];
    }

    public function prepareConfig(FieldFilterInterface $filter, $value): FilterMetaInterface
    {
        $config = $this->configFactory->create();
        $config->setValue($value);
        $filter->getExtensionAttributes()->setStoreFilter($config);

        return $this;
    }

    public function getValue(FieldFilterInterface $filter)
    {
        if ($filter->getExtensionAttributes()->getStoreFilter()) {
            return $filter->getExtensionAttributes()->getStoreFilter()->getValue();
        }

        return null;
    }

    private function getOptions(): array
    {
        $options = $this->options->toOptionArray();
        if (empty($options)) {
            return [];
        }

        $removeAllStoreViews = $this->gwsAdapter->getRole() && !$this->gwsAdapter->getRole()->getIsAll();
        if ($removeAllStoreViews) {
            $options = array_filter($options, static function ($option) {
                return $option['value'] !== '0';
            });
        }

        return $options;
    }
}
