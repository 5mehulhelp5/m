<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Console\Command;

use Amasty\ImportExportCore\Api\Profile\ProfileManagerInterface;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowProfileRunnersList extends Command
{
    public const COMMAND_NAME = 'am-import-export:show-profile-runners-list';

    /**
     * @var ProfileManagerInterface
     */
    private $profileManager;

    public function __construct(
        ProfileManagerInterface $profileManager,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->profileManager = $profileManager;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Show Profile Runner Type List.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->profileManager->getProfileRunnersList();
        foreach ($response as $type) {
            $output->writeln($type);
        }

        return Cli::RETURN_SUCCESS;
    }
}
