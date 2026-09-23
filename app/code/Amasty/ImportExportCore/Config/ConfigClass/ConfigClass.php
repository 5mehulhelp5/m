<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Config\ConfigClass;

use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterface;

class ConfigClass implements ConfigClassInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $arguments = [];

    public function __construct(
        string $name,
        ?string $baseType = '',
        ?array $arguments = []
    ) {
        if (!empty($baseType) && !is_subclass_of($name, $baseType)) {
            throw new \LogicException(
                'Class ' . $name . ' doesn\'t implement ' . $baseType
            );
        }

        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): ?array
    {
        return $this->arguments;
    }
}
