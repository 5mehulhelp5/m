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

class RunProfileCommand extends Command
{
    public const COMMAND_NAME = 'am-import-export:run-profile';
    public const INPUT_KEY_TYPE = 'type';
    public const INPUT_KEY_PROFILE_ID = 'profile-id';

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
            ->setDescription('Run Import/Export Profile.');

        $this->addArgument(
            self::INPUT_KEY_TYPE,
            InputArgument::REQUIRED,
            'Import/Export profile runner type'
        );
        $this->addArgument(
            self::INPUT_KEY_PROFILE_ID,
            InputArgument::REQUIRED,
            'Profile ID'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument(self::INPUT_KEY_TYPE);
        $profileId = (int) $input->getArgument(self::INPUT_KEY_PROFILE_ID);

        $this->cliAreaFlag->setIsCliArea(true);
        $response = $this->profileManager->run($type, $profileId);
        $this->cliAreaFlag->setIsCliArea(false);

        $output->writeln(
            sprintf('%s: %d', RunProfileResponseInterface::STATUS_CODE, $response->getStatusCode())
        );
        $output->writeln(
            sprintf('%s: %s', RunProfileResponseInterface::MESSAGE, $response->getMessage())
        );
        if ($response->getIdentity()) {
            $output->writeln(
                sprintf('%s: %s', RunProfileResponseInterface::IDENTITY, $response->getIdentity())
            );
            $output->writeln(
                sprintf(
                    '<comment>You can watch process status by using "%s %s %s" command</comment>',
                    CheckProfileStatus::COMMAND_NAME,
                    $type,
                    $response->getIdentity()
                )
            );
        }

        return Cli::RETURN_SUCCESS;
    }
}
