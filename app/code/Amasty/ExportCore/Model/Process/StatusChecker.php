<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\Process;

use Amasty\ExportCore\Api\ExportResultInterface;
use Amasty\ExportCore\Model\ConfigProvider;
use Amasty\ExportCore\Processing\JobManager;
use Amasty\ImportExportCore\Api\Process\ProcessStatusInterface;
use Amasty\ImportExportCore\Api\Process\StatusCheckerInterface;
use Amasty\ImportExportCore\Api\Process\ProcessStatusInterfaceFactory;
use Amasty\ImportExportCore\Model\OptionSource\ProcessStatusCheckMode;
use Magento\Framework\App\ObjectManager;

class StatusChecker implements StatusCheckerInterface
{
    public const PROCESS_STARTED_CHECK_LIMIT = 3;

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var ProcessStatusInterfaceFactory
     */
    private $processStatusFactory;

    /**
     * @var ProcessRepository
     */
    private $processRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        JobManager $jobManager,
        ProcessStatusInterfaceFactory $processStatusFactory,
        ProcessRepository $processRepository,
        ?ConfigProvider $configProvider = null
    ) {
        $this->jobManager = $jobManager;
        $this->processStatusFactory = $processStatusFactory;
        $this->processRepository = $processRepository;
        $this->configProvider = $configProvider ?? ObjectManager::getInstance()->get(ConfigProvider::class);
    }

    public function check(string $processIdentity): ProcessStatusInterface
    {
        /**
         * @var Process $process
         * @var ExportResultInterface $exportResult
         */
        [$process, $exportResult] = $this->jobManager->watchJob($processIdentity)->getJobState();
        if ($error = $this->checkError($process, $exportResult)) {
            return $error;
        }

        $processStatus = $this->processStatusFactory->create();
        if ($exportResult === null) {
            $processStatus->setStatus('starting');
            $processStatus->setProceed(0);
            $processStatus->setTotal(0);
            $processStatus->setMessages([
                [
                    'type' => 'info',
                    'message' => __('Process Started')
                ]
            ]);
        } else {
            $processStatus->setStatus($process->getStatus());
            $processStatus->setProceed($exportResult->getRecordsProcessed());
            $processStatus->setTotal($exportResult->getTotalRecords());
            $processStatus->setMessages($exportResult->getMessages());
        }

        return $processStatus;
    }

    private function checkError(Process $process, ?ExportResultInterface $exportResult): ?ProcessStatusInterface
    {
        $processStatusCheckMode = $this->configProvider->getProcessStatusCheckMode();
        if ($processStatusCheckMode === ProcessStatusCheckMode::PID) {
            return $this->checkByPid($process, $exportResult);
        }

        if ($processStatusCheckMode === ProcessStatusCheckMode::STATUS) {
            return $this->checkByStatus($process, $exportResult);
        }

        return null;
    }

    private function checkByPid(Process $process, ?ExportResultInterface $exportResult): ?ProcessStatusInterface
    {
        $isProcessAlive = $process->getPid() !== null
            && $this->jobManager->isPidAlive((int)$process->getPid());
        if (!$isProcessAlive) {
            $currentStatus = $this->processRepository->checkProcessStatus((string)$process->getIdentity());
            $processStatus = $this->processStatusFactory->create();
            if (!$exportResult) { //when error with starting process before first action ran
                $errorMsg = __(
                    'The export process failed to launch. Please, check your PHP executable path or'
                    . ' see log for more details.'
                );
            } elseif (!in_array($currentStatus, [Process::STATUS_SUCCESS, Process::STATUS_FAILED], true)) {
                $errorMsg = __(
                    'The system process failed. For an error details please make sure that Debug mode is enabled '
                    . 'and see %1',
                    ConfigProvider::DEBUG_LOG_PATH
                );
                $exportResult->logMessage(ExportResultInterface::MESSAGE_CRITICAL, $errorMsg);
            }

            if (isset($errorMsg)) {
                $processStatus->setMessages([
                    [
                        'type' => ExportResultInterface::MESSAGE_CRITICAL,
                        'message' => $errorMsg
                    ]
                ]);

                return $processStatus;
            }
        }

        return null;
    }

    private function checkByStatus(Process $process, ?ExportResultInterface $exportResult): ?ProcessStatusInterface
    {
        $currentStatus = $this->processRepository->checkProcessStatus((string)$process->getIdentity());
        $processStatus = $this->processStatusFactory->create();
        $hasErrorMessages = $exportResult !== null && $this->hasErrorMessages($exportResult);

        if ($currentStatus === Process::STATUS_FAILED && !$hasErrorMessages) {
            $errorMsg = __(
                'The system process failed. For an error details please make sure that Debug mode is enabled '
                . 'and see %1',
                ConfigProvider::DEBUG_LOG_PATH
            );
        }

        if (($currentStatus === Process::STATUS_PENDING)
            && $this->checkProcessFailedToStart((string)$process->getIdentity())
        ) {
            $errorMsg = __(
                'The export process failed to launch. Please, check your PHP executable path or'
                . ' see log for more details.'
            );
        }

        if (isset($errorMsg)) {
            $processStatus->setMessages([
                [
                    'type' => ExportResultInterface::MESSAGE_CRITICAL,
                    'message' => $errorMsg
                ]
            ]);

            return $processStatus;
        }

        return null;
    }

    private function checkProcessFailedToStart(string $identity, int $idx = 0): bool
    {
        $currentStatus = $this->processRepository->checkProcessStatus($identity);
        if ($currentStatus === Process::STATUS_PENDING) {
            if ($idx <= self::PROCESS_STARTED_CHECK_LIMIT) {
                //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                sleep(3); //status check can be run before export process starts - waiting 3s before asserting exception
                return $this->checkProcessFailedToStart($identity, ++$idx); //trying recheck status 3 times
            }
            return true;
        }

        return false;
    }

    private function hasErrorMessages(ExportResultInterface $exportResult): bool
    {
        $messages = $exportResult->getMessages();
        foreach ($messages as $message) {
            if (in_array(
                $message['type'] ?? null,
                [ExportResultInterface::MESSAGE_CRITICAL, ExportResultInterface::MESSAGE_ERROR],
                true
            )) {
                return true;
            }
        }

        return false;
    }
}
