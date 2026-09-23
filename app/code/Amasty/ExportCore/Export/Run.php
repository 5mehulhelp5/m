<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\ExportResultInterface;

class Run
{
    public const DEFAULT_STRATEGY = 'export';

    /**
     * @var ExportStrategy[]
     */
    private $strategies;

    public function __construct(
        array $strategies
    ) {
        $this->strategies = $strategies;
    }

    public function execute(
        ProfileConfigInterface $profileConfig,
        string $processIdentity
    ): ExportResultInterface {
        $strategy = $profileConfig->getStrategy() ?? self::DEFAULT_STRATEGY;
        if (empty($this->strategies[$strategy])) {
            throw new \LogicException('Strategy "' . $strategy . '" does not exist');
        }

        return $this->strategies[$strategy]->run($profileConfig, $processIdentity);
    }
}
