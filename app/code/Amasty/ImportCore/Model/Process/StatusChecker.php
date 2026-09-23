<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Model\Process;

use Amasty\ImportCore\Api\ImportResultInterface;
use Amasty\ImportCore\Import\ImportResult;
use Amasty\ImportCore\Model\ConfigProvider;
use Amasty\ImportCore\Processing\JobManager;
use Amasty\ImportExportCore\Api\Process\ProcessStatusInterface;
use Amasty\ImportExportCore\Api\Process\ProcessStatusInterfaceFactory;
use Amasty\ImportExportCore\Api\Process\StatusCheckerInterface;
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
        /** @var $process Process */
        /** @var $importResult ImportResultInterface|ImportResult */
        [$process, $importResult] = $this->jobManager->watchJob($processIdentity)
            ->getJobState();

        if ($error = $this->checkError($process, $importResult)) {
            return $error;
        }

        $processStatus = $this->processStatusFactory->create();
        if ($importResult === null) {
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
            $resultMessages =
                array_merge(
                    $importResult->getMessages(),
                    $importResult->getPreparedValidationMessages(),
                    $importResult->getFilteringMessages()
                );
            $processStatus->setStatus($process->getStatus());
            $processStatus->setProceed($importResult->getRecordsProcessed());
            $processStatus->setTotal($importResult->getTotalRecords());
            $processStatus->setMessages($resultMessages);
        }

        return $processStatus;
    }

    private function checkError(Process $process, ?ImportResultInterface $importResult): ?ProcessStatusInterface
    {
        $processStatusCheckMode = $this->configProvider->getProcessStatusCheckMode();
        if ($processStatusCheckMode === ProcessStatusCheckMode::PID) {
            return $this->checkByPid($process, $importResult);
        }

        if ($processStatusCheckMode === ProcessStatusCheckMode::STATUS) {
            return $this->checkByStatus($process, $importResult);
        }

        return null;
    }

    private function checkByPid(Process $process, ?ImportResultInterface $importResult): ?ProcessStatusInterface
    {
        $isProcessAlive = $process->getPid() !== null
            && $this->jobManager->isPidAlive((int)$process->getPid());
        if (!$isProcessAlive) {
            $processStatus = $this->processStatusFactory->create();
            $currentStatus = $this->processRepository->checkProcessStatus((string)$process->getIdentity());
            if (!$importResult) { //when error with starting process before first action ran
                $errorMsg = __(
                    'The import process failed to launch. Please, check your PHP executable path or'
                    . ' see log for more details.'
                );
            } elseif (!in_array($currentStatus, [Process::STATUS_SUCCESS, Process::STATUS_FAILED])) {
                $errorMsg = __(
                    'The system process failed. For an error details please make sure that Debug mode is enabled '
                    . 'and see %1',
                    ConfigProvider::DEBUG_LOG_PATH
                );
                $importResult->logMessage(ImportResultInterface::MESSAGE_CRITICAL, $errorMsg);
            }

            if (isset($errorMsg)) {
                $processStatus->setMessages([
                    [
                        'type' => ImportResultInterface::MESSAGE_CRITICAL,
                        'message' => $errorMsg
                    ]
                ]);

                return $processStatus;
            }
        }

        return null;
    }

    private function checkByStatus(Process $process, ?ImportResultInterface $importResult): ?ProcessStatusInterface
    {
        $currentStatus = $this->processRepository->checkProcessStatus((string)$process->getIdentity());
        $processStatus = $this->processStatusFactory->create();
        $hasErrorMessages = $importResult !== null && $this->hasErrorMessages($importResult);

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
                'The import process failed to launch. Please, check your PHP executable path or'
                . ' see log for more details.'
            );
        }

        if (isset($errorMsg)) {
            $processStatus->setMessages([
                [
                    'type' => ImportResultInterface::MESSAGE_CRITICAL,
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
                sleep(3); //status check can be run before import process starts - waiting 3s before asserting exception
                return $this->checkProcessFailedToStart($identity, ++$idx); //trying recheck status 3 times
            }
            return true;
        }

        return false;
    }

    private function hasErrorMessages(ImportResultInterface $importResult): bool
    {
        $messages = $importResult->getMessages();
        foreach ($messages as $message) {
            if (in_array(
                $message['type'] ?? null,
                [ImportResultInterface::MESSAGE_CRITICAL, ImportResultInterface::MESSAGE_ERROR],
                true
            )) {
                return true;
            }
        }

        return false;
    }
}
