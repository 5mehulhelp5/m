<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model;

use Amasty\ImportExportCore\Api\Profile\ProfileManagerInterface;
use Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface;
use Amasty\ImportExportCore\Model\Profile\ProfileRunnersPool;

class ProfileManager implements ProfileManagerInterface
{
    /**
     * @var ProfileRunnersPool
     */
    private $profileRunnersPool;

    public function __construct(
        ProfileRunnersPool $profileRunnersPool
    ) {
        $this->profileRunnersPool = $profileRunnersPool;
    }

    public function run(string $type, int $profileId): RunProfileResponseInterface
    {
        $profileRunner = $this->profileRunnersPool->get($type);
        if (!$profileRunner) {
            throw new \InvalidArgumentException((string) __('Profile runner with type "%1" not found', $type));
        }

        return $profileRunner->run($profileId);
    }

    public function checkProfileRunStatus(string $type, string $identityId): RunProfileResponseInterface
    {
        $profileRunner = $this->profileRunnersPool->get($type);
        if (!$profileRunner) {
            throw new \InvalidArgumentException((string) __('Profile runner with type "%1" not found', $type));
        }

        return $profileRunner->check($identityId);
    }

    public function getProfileRunnersList(): array
    {
        return array_keys($this->profileRunnersPool->getAll());
    }
}
