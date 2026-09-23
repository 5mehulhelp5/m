<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\ThemeLayoutPro\Console\Command;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Codazon\ThemeLayoutPro\Helper\Data as ThemeHelper;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\App\State;

/**
 * Class ProductAttributesCleanUp
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Import extends \Symfony\Component\Console\Command\Command
{
    protected $helper;

    protected $themeData;

    protected $appState;
    protected $objectManager;
    protected $fixtureManager;

    /**
     * {@inheritdoc}
     */

    protected function init()
    {
        $this->themeData = \Magento\Framework\App\ObjectManager::getInstance()->get(\Codazon\ThemeLayoutPro\Model\Data::class);
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->fixtureManager = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Setup\SampleData\FixtureManager::class);
    }

    protected function configure()
    {
        $this->setName('codazon:theme-assets:import');
        $this->setDescription('Import a new homepage of Codazon theme.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();
        $output->setDecorated(true);


        //$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, 1);
        //$progress->setFormat('<comment>%message%</comment> %current%/%max% [%bar%] %percent:3s%% %elapsed%');


        try {
            $formatter = $output->getFormatter();
            $formatter->setStyle('title', new OutputFormatterStyle('magenta'));

            $importModel = $this->objectManager->get('\Codazon\ThemeLayoutPro\Model\Import');
            $file = $this->fixtureManager->getFixture('Codazon_ThemeLayoutPro::fixtures/themelayout_homepage_entity.csv');
            $importModel->importMainContent(null, $file);
            $output->writeln("");
            $output->writeln("<info>Codzon Theme Homepage are imported.</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            //$this->attributeResource->rollBack();

            $output->writeln("");
            $output->writeln("<error>{$exception->getMessage()}</error>");
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}
