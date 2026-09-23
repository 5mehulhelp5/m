<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Model\Profile;

use Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface;

interface ProfileRunnerWrapperInterface
{
    /**
     * Performs run action of a particular profile runner and build response
     *
     * @param int $profileId
     * @return RunProfileResponseInterface
     */
    public function run(int $profileId): RunProfileResponseInterface;

    /**
     * Check status of the runner by process identity
     *
     * @param string $identity
     * @return RunProfileResponseInterface
     */
    public function check(string $identity): RunProfileResponseInterface;
}
