<?php

namespace HarrisStreet\CoreConfigData;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends AbstractImpex
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('hs:ccd:import')
            ->addArgument('env', InputArgument::REQUIRED, 'Environment name')
            ->setDescription('Import and update Core_Config_Data settings and data for environment');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output, TRUE);
        if ($this->initMagento()) {

            $environment = $input->getArgument('env');

            var_dump($environment);

            exit;

            $helper = \Mage::helper('limesoda_environmentconfiguration');

            // Deactivating auto-exiting after command execution
            $this->getApplication()->setAutoExit(FALSE);

            $variables = $helper->getVariables($environment);
            $search    = array_keys($variables);
            $replace   = array_values($variables);

            foreach ($helper->getCommands($environment) as $command) {
                $value = str_replace($search, $replace, strval($command));
                $input = new StringInput($value);
                $this->getApplication()->run($input, $output);
            }

            // Reactivating auto-exiting after command execution
            $this->getApplication()->setAutoExit(TRUE);
        }
    }
}