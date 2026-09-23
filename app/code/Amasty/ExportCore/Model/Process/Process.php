<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\Process;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @method self setPid(int|null $pid)
 * @method int|null getPid()
 * @method self setExportResult(string|null $exportResult)
 * @method string|null getExportResult()
 * @method self setStatus(string $status)
 * @method string getStatus()
 * @method self setEntityCode(string $code)
 * @method string getEntityCode()
 * @method self setIdentity(string $identity)
 * @method string getIdentity()
 * @method self setFinished(bool $finished)
 * @method string getFinished()
 */
class Process extends AbstractModel
{
    public const ID = 'id';
    public const ENTITY_CODE = 'entity_code';
    public const PID = 'pid';
    public const STATUS = 'status';
    public const FINISHED = 'finished';
    public const EXPORT_RESULT = 'export_result';
    public const PROFILE_CONFIG = 'profile_config';
    public const IDENTITY = 'identity';

    public const STATUS_PENDING = 'pending';
    public const STATUS_STARTING = 'starting';
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Process::class);
        $this->setIdFieldName(self::ID);
    }

    public function setProfileConfig(ProfileConfigInterface $profileConfig)
    {
        return $this->setData('profile_config_model', $profileConfig);
    }

    public function getProfileConfig(): ProfileConfigInterface
    {
        return $this->_getData('profile_config_model');
    }

    public function setProfileConfigSerialized($profileConfigSerialized)
    {
        return $this->setData('profile_config', $profileConfigSerialized);
    }

    public function getProfileConfigSerialized(): string
    {
        return $this->_getData('profile_config');
    }
}
