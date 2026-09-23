<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Console\Command;

use Amasty\ImportExportCore\Api\Profile\ProfileManagerInterface;
use Amasty\ImportExportCore\Api\Profile\RunProfileResponseInterface;
use Amasty\ImportExportCore\Model\Cli\CliAreaFlag;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckProfileStatus extends Command
{
    public const COMMAND_NAME = 'am-import-export:check-profile-status';
    public const INPUT_KEY_TYPE = 'type';
    public const INPUT_KEY_IDENTITY = 'identity';

    /**
     * @var ProfileManagerInterface
     */
    private $profileManager;

    /**
     * @var CliAreaFlag
     */
    private $cliAreaFlag;

    public function __construct(
        ProfileManagerInterface $profileManager,
        CliAreaFlag $cliAreaFlag,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->profileManager = $profileManager;
        $this->cliAreaFlag = $cliAreaFlag;
    }

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Check Import/Export Profile Run Status.');

        $this->addArgument(
            self::INPUT_KEY_TYPE,
            InputArgument::REQUIRED,
            'Import/Export profile runner type'
        );
        $this->addArgument(
            self::INPUT_KEY_IDENTITY,
            InputArgument::REQUIRED,
            'Identity ID'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument(self::INPUT_KEY_TYPE);
        $identity = $input->getArgument(self::INPUT_KEY_IDENTITY);

        $this->cliAreaFlag->setIsCliArea(true);
        $response = $this->profileManager->checkProfileRunStatus($type, $identity);
        $this->cliAreaFlag->setIsCliArea(false);
        $output->writeln(
            sprintf('%s: %d', RunProfileResponseInterface::STATUS_CODE, $response->getStatusCode())
        );
        $output->writeln(
            sprintf('<info>%s</info>', $response->getMessage())
        );

        return Cli::RETURN_SUCCESS;
    }
}
