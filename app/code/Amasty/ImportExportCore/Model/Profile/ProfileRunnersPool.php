<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\Profile;

class ProfileRunnersPool
{
    /**
     * @var ProfileRunnerWrapperInterface[]
     */
    private $profileRunners;

    public function __construct(
        array $profileRunners = []
    ) {
        $this->initProfileRunners($profileRunners);
    }

    public function get(string $name): ?ProfileRunnerWrapperInterface
    {
        return $this->profileRunners[$name] ?? null;
    }

    /**
     * @return ProfileRunnerWrapperInterface[]
     */
    public function getAll(): array
    {
        return $this->profileRunners;
    }

    /**
     * @param ProfileRunnerWrapperInterface[] $profileRunners
     * @return void
     */
    private function initProfileRunners(array $profileRunners): void
    {
        foreach ($profileRunners as $profileRunner) {
            if (!$profileRunner instanceof ProfileRunnerWrapperInterface) {
                throw new \LogicException(
                    __(
                        'Class %1 must implement %2 interface',
                        get_class($profileRunner),
                        ProfileRunnerWrapperInterface::class
                    )->render()
                );
            }
        }
        $this->profileRunners = $profileRunners;
    }
}
