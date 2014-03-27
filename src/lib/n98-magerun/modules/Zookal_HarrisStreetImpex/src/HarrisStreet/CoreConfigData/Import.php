<?php

namespace HarrisStreet\CoreConfigData;

use HarrisStreet\CoreConfigData\Importer\ImporterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends AbstractImpex
{
    /**
     * @var ImporterInterface
     */
    protected $_importerInstance = NULL;

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('hs:ccd:import')
            ->addArgument('folder', InputArgument::REQUIRED, 'Import folder name')
            ->addArgument('env', InputArgument::REQUIRED, 'Environment name')
            ->setDescription('HarrisStreet: Import and update Core_Config_Data settings and data for environment');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->_importerInstance = $this->_getFormatClass();

        $folder = $input->getArgument('folder');
        $env    = $input->getArgument('env');

        var_dump([$folder, $env]);

        exit;

        $this->getApplication()->setAutoExit(FALSE);

        $variables = $helper->getVariables($environment);
        $search    = array_keys($variables);
        $replace   = array_values($variables);

        foreach ($helper->getCommands($environment) as $command) {
            $value = str_replace($search, $replace, strval($command));
            $input = new StringInput($value);
            $this->getApplication()->run($input, $output);
        }

        $this->getApplication()->setAutoExit(TRUE);
    }
}