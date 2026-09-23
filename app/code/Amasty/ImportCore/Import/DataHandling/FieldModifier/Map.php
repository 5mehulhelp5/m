<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;

class Map extends AbstractModifier implements FieldModifierInterface
{
    /**
     * Map param key
     */
    public const MAP = 'map';

    /**
     * Key for flag that defines if the text contains multiple values
     */
    public const IS_MULTIPLE = 'is_multiple';

    /**
     * Key for multiple values parts delimiter
     */
    public const DELIMITER = 'delimiter';

    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->config = $config;
    }

    public function transform($value)
    {
        $map = $this->config[self::MAP] ?? [];
        if ($this->config[self::IS_MULTIPLE] && !empty($value)) {
            $delimiter = $this->config[self::DELIMITER] ?? ',';
            $parts = explode($delimiter, $value);
            $result = [];
            foreach ($parts as $valuePart) {
                if (array_key_exists($valuePart, $map)) {
                    $result[] = $map[$valuePart];
                }
            }

            return implode(',', $result);
        }

        return $map[$value] ?? $value;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Map')->getText();
    }
}
