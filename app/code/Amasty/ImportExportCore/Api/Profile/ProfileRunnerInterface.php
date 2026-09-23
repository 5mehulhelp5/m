<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Api\Profile;

interface ProfileRunnerInterface
{
    /**
     * @param int $profileId
     * @param \Closure|null $profileConfigModifier
     * @return string
     */
    public function run(int $profileId, ?\Closure $profileConfigModifier = null): string;

    /**
     * @param int $profileId
     * @param \Closure|null $profileConfigModifier
     * @return string
     */
    public function manualRun(int $profileId, ?\Closure $profileConfigModifier = null): string;

    /**
     * @param int $profileId
     * @param \Closure|null $profileConfigModifier
     * @return \Magento\Framework\Api\ExtensibleDataInterface
     */
    public function prepareProfileConfig(
        int $profileId,
        ?\Closure $profileConfigModifier = null
    );
}
