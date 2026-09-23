<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Config\Xml;

use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterface;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ArgumentInterfaceFactory;

class ArgumentsPrepare
{
    /**
     * @var ArgumentInterfaceFactory
     */
    private $argumentFactory;

    public function __construct(ArgumentInterfaceFactory $argumentFactory)
    {
        $this->argumentFactory = $argumentFactory;
    }

    /**
     * Converts raw arguments data array into argument config instances
     *
     * @param array $arguments
     * @return ArgumentInterface[]
     */
    public function execute($arguments): array
    {
        if (empty($arguments)) {
            return [];
        }

        return $this->prepareArguments($arguments);
    }

    /**
     * @param array $arguments
     * @return ArgumentInterface[]
     */
    private function prepareArguments($arguments): array
    {
        $result = [];

        foreach ($arguments as $argumentData) {
            $argument = $this->argumentFactory->create();
            $argument->setName($argumentData['name']);
            $argument->setType($argumentData['xsi:type']);
            if ($argument->getType() === 'array' && !empty($argumentData['item'])) {
                $argument->setItems($this->prepareArguments($argumentData['item']));
            } else {
                $argument->setValue($argumentData['value']);
            }

            $result[] = $argument;
        }

        return $result;
    }
}
