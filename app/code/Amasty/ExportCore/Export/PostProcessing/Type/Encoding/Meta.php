<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\PostProcessing\Type\Encoding;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Export\Config\ProfileConfig;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

class Meta implements FormInterface
{
    public const TYPE_ID = 'encoding';

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Output File Encoding'),
                        'dataType' => 'select',
                        'formElement' => 'select',
                        'visible' => true,
                        'componentType' => 'select',
                        'dataScope' => 'post_processors.' . self::TYPE_ID,
                        'default' => ConfigInterface::DEFAULT_ENCODING,
                        'options' => $this->getOptions()
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $profileConfig->getExtensionAttributes()->setEncoding(
            $request->getParam(ProfileConfig::POST_PROCESSORS)[self::TYPE_ID]
        );

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        $encoding = $profileConfig->getExtensionAttributes()->getEncoding() ?? ConfigInterface::DEFAULT_ENCODING;

        return [self::TYPE_ID => $encoding];
    }

    private function getOptions(): array
    {
        $options = [];
        $encodings = mb_list_encodings();
        sort($encodings);

        foreach ($encodings as $encoding) {
            $options[] = ['label' => $encoding, 'value' => $encoding];
        }

        return $options;
    }
}
