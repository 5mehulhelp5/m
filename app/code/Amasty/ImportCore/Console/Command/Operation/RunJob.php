<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Console\Command\Operation;

use Amasty\ImportCore\Import\Run;
use Amasty\ImportCore\Model\Process\ProcessRepository;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class RunJob
{
    /**
     * @var Run
     */
    private $runner;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ProcessRepository
     */
    private $processRepository;

    public function __construct(
        ProcessRepository $processRepository,
        Run $runner,
        State $appState
    ) {
        $this->runner = $runner;
        $this->appState = $appState;
        $this->processRepository = $processRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $process = $this->processRepository->getByIdentity($input->getArgument('identity'));

            // Sometimes area code should be set
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$this->runner, 'execute'],
                [$process->getProfileConfig(), $input->getArgument('identity')]
            );
        } catch (\Exception $e) {
            $this->processRepository->markAsFailed(
                $input->getArgument('identity'),
                $e->getMessage()
            );
        }
    }
}
