<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Processing;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Model\ConfigProvider;
use Amasty\ExportCore\Model\Process\ProcessRepository;
use Amasty\ImportExportCore\Utils\CliPhpResolver;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Shell;

class JobManager
{
    /**
     * @var JobWatcherFactory
     */
    private $jobWatcherFactory;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var ProcessRepository
     */
    private $processRepository;

    /**
     * @var CliPhpResolver
     */
    private $cliPhpResolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        JobWatcherFactory $jobWatcherFactory,
        ProcessRepository $processRepository,
        CliPhpResolver $cliPhpResolver,
        ConfigProvider $configProvider,
        Shell $shell,
        Filesystem $filesystem
    ) {
        $this->jobWatcherFactory = $jobWatcherFactory;
        $this->shell = $shell;
        $this->processRepository = $processRepository;
        $this->cliPhpResolver = $cliPhpResolver;
        $this->configProvider = $configProvider;
        $this->filesystem = $filesystem;
    }

    public function requestJob(ProfileConfigInterface $profileConfig, ?string $identity = null): JobWatcher
    {
        try {
            $matchingProcess = $this->processRepository->getByIdentity($identity);

            if ($matchingProcess->getPid() && $this->isPidAlive((int)$matchingProcess->getPid())) {
                return $this->jobWatcherFactory->create([
                    'processIdentity' => $identity
                ]);
            } else {
                $this->processRepository->delete($matchingProcess);
            }
        } catch (NoSuchEntityException $e) {
            ;
        }

        $identity = $this->processRepository->initiateProcess($profileConfig, $identity);

        $phpPath = $this->cliPhpResolver->getExecutablePath();
        $memoryLimit = $this->configProvider->isNoMemoryLimit() ? ' -dmemory_limit=-1' : '';
        if ($this->configProvider->isDebugEnabled()) {
            $reader = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
            $logAbsolutePath = $reader->getAbsolutePath(ConfigProvider::DEBUG_LOG_PATH);
            $redirectOutput = ' >> ' . $logAbsolutePath;
        } else {
            $redirectOutput = ' > /dev/null';
        }

        $pid = $this->shell->execute(
            $phpPath . $memoryLimit . ' %s amasty:export:run-job %s' . $redirectOutput . ' & echo $!',
            [
                BP . '/bin/magento',
                $identity
            ]
        );
        $this->processRepository->updateProcessPid($identity, (int)$pid);

        return $this->jobWatcherFactory->create(['processIdentity' => $identity]);
    }

    public function watchJob(string $identity): JobWatcher
    {
        $matchingProcess = $this->processRepository->getByIdentity($identity);

        return $this->jobWatcherFactory->create([
            'processIdentity' => $matchingProcess->getIdentity(),
            'pid'             => (int)$matchingProcess->getPid()
        ]);
    }

    public function isPidAlive(int $pid): bool
    {
        //phpcs:ignore
        return false !== posix_getpgid($pid);
    }
}
