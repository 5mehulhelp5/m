<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Profile;

interface ProfileManagerInterface
{
    /**
     * @param string $type
     * @param int $profileId
     * @return \Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface
     * @throws \InvalidArgumentException
     */
    public function run(string $type, int $profileId): RunProfileResponseInterface;

    /**
     * @param string $type
     * @param string $identityId
     * @return \Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface
     * @throws \InvalidArgumentException
     */
    public function checkProfileRunStatus(string $type, string $identityId): RunProfileResponseInterface;

    /**
     * @return string[]
     */
    public function getProfileRunnersList(): array;
}
