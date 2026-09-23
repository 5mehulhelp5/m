<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Model\Process;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\ExportResultInterface;
use Amasty\ExportCore\Api\ExportResultInterfaceFactory;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\ExportCore\Model\Process\ResourceModel\CollectionFactory;
use Amasty\ExportCore\Model\Process\ResourceModel\Process as ProcessResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProcessRepository
{
    public const IDENTITY = 'process_id';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProcessResource
     */
    private $processResource;

    /**
     * @var array
     */
    private $processes = [];

    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * @var ExportResultInterfaceFactory
     */
    private $exportResultFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        CollectionFactory $collectionFactory,
        Serializer $serializer,
        ProcessFactory $processFactory,
        ProcessResource $processResource,
        ExportResultInterfaceFactory $exportResultFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->processResource = $processResource;
        $this->processFactory = $processFactory;
        $this->exportResultFactory = $exportResultFactory;
        $this->serializer = $serializer;
    }

    public function getByIdentity($identity): Process
    {
        if (!isset($this->processes[$identity])) {
            /** @var Process $process */
            $process = $this->processFactory->create();
            $this->processResource->load($process, $identity, Process::IDENTITY);
            if (!$process->getId()) {
                throw new NoSuchEntityException(__('Process with specified identity "%1" not found.', $identity));
            }

            $process->setProfileConfig(
                $this->serializer->unserialize(
                    $process->getProfileConfigSerialized(),
                    ProfileConfigInterface::class
                )
            );

            $this->processes[$identity] = $process;
        }

        return $this->processes[$identity];
    }

    public function delete(Process $process)
    {
        try {
            $this->processResource->delete($process);
            unset($this->processes[$process->getIdentity()]);
        } catch (\Exception $e) {
            if ($process->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove batch with ID %1. Error: %2',
                        [$process->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove batch. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function updateProcessPid(string $identity, int $pid): void
    {
        try {
            $process = $this->getByIdentity($identity);
        } catch (NoSuchEntityException $e) {
            return;
        }
        $process->setPid($pid);
        $this->processResource->save($process);
    }

    public function updateProcess(ExportProcessInterface $exportProcess)
    {
        try {
            $process = $this->getByIdentity($exportProcess->getIdentity());
        } catch (NoSuchEntityException $e) {
            return;
        }

        $process
            ->setStatus(Process::STATUS_RUNNING)
            ->setPid(getmypid())
            ->setExportResult($exportProcess->getExportResult()->serialize());
        $this->processResource->save($process);
    }

    public function finalizeProcess(ExportProcessInterface $exportProcess)
    {
        try {
            $process = $this->getByIdentity($exportProcess->getIdentity());
        } catch (NoSuchEntityException $e) {
            return;
        }

        $exportResult = $exportProcess->getExportResult();
        $process
            ->setStatus($exportResult->isFailed() ? Process::STATUS_FAILED : Process::STATUS_SUCCESS)
            ->setFinished(true)
            ->setPid(null)
            ->setExportResult($exportResult->serialize());
        $this->processResource->save($process);
    }

    public function markAsFailed(string $identity, ?string $errorMessage = null)
    {
        try {
            $process = $this->getByIdentity($identity);
        } catch (NoSuchEntityException $e) {
            return;
        }

        if ($process->getExportResult()) {
            /** @var ExportResultInterface $result */
            $result = $this->exportResultFactory->create();
            $result->unserialize($process->getExportResult());
            if ($errorMessage) {
                $result->logMessage(ExportResultInterface::MESSAGE_CRITICAL, $errorMessage);
            }
            $result->terminateExport(true);
            $serializedResult = $result->serialize();
        } else {
            $serializedResult = null;
        }

        $process
            ->setStatus(Process::STATUS_FAILED)
            ->setFinished(true)
            ->setPid(null)
            ->setExportResult($serializedResult);
        $this->processResource->save($process);
    }

    public function initiateProcess(ProfileConfigInterface $profileConfig, ?string $identity = null): string
    {
        if (empty($identity)) {
            $identity = $this->generateNewIdentity();
        }

        $process = $this->processFactory->create();
        $process
            ->setIdentity($identity)
            ->setProfileConfigSerialized(
                $this->serializer->serialize($profileConfig, ProfileConfigInterface::class)
            )->setStatus(Process::STATUS_PENDING)
            ->setPid(null)
            ->setEntityCode($profileConfig->getEntityCode())
            ->setExportResult(null);
        $this->processResource->save($process);

        return $identity;
    }

    public function checkProcessStatus(string $identity): string
    {
        $process = $this->processFactory->create();
        $this->processResource->load($process, $identity, Process::IDENTITY);

        return (string)$process->getStatus();
    }

    public function updateProcessStatusByIdentity(string $identity, string $status): void
    {
        $process = $this->processFactory->create();
        $this->processResource->load($process, $identity, Process::IDENTITY);
        if ($process->getId()) {
            $process->setStatus($status);
            $this->processResource->save($process);
        }
    }

    public function generateNewIdentity()
    {
        return uniqid();
    }
}
