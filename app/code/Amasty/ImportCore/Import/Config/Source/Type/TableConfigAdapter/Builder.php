<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\Source\Type\TableConfigAdapter;

use Amasty\ImportCore\Import\Config\Source\Type\TableConfigAdapter\Builder\BuilderInterface;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\Config\Source\Type\TableConfigAdapterFactory;

class Builder
{
    /**
     * @var BuilderInterface[]
     */
    private $builders;

    /**
     * @var TableConfigAdapterFactory
     */
    private $tableConfigAdapterFactory;

    public function __construct(
        TableConfigAdapterFactory $tableConfigAdapterFactory,
        array $builders
    ) {
        $this->checkBuilderInstance($builders);
        $this->builders = $builders;
        $this->tableConfigAdapterFactory = $tableConfigAdapterFactory;
    }

    public function build(ProfileConfig $profileConfig)
    {
        $data = [];
        foreach ($this->builders as $builder) {
            $data = $builder->build($profileConfig, $data);
        }

        return $this->tableConfigAdapterFactory->create(['data' => $data]);
    }

    /**
     * @param array $builders
     * @throws \InvalidArgumentException
     * @return void
     */
    private function checkBuilderInstance(array $builders): void
    {
        foreach ($builders as $builderKey => $builder) {
            if (!$builder instanceof BuilderInterface) {
                throw new \InvalidArgumentException(
                    'The builder instance "' . $builderKey . '" must implement ' . BuilderInterface::class
                );
            }
        }
    }
}
