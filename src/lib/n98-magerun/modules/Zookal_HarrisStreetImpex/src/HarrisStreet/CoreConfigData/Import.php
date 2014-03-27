<?php

namespace HarrisStreet\CoreConfigData;

use HarrisStreet\CoreConfigData\Importer\ImporterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Import extends AbstractImpex
{
    /**
     * @var string
     */
    protected $_folder = NULL;

    /**
     * @var array
     */
    protected $_environment = NULL;

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
            ->addArgument('env', InputArgument::REQUIRED, 'Environment name. SubEnvs separated by slash: development/developer01')
            ->addOption('base', NULL, InputOption::VALUE_OPTIONAL, 'Base folder name', 'base')
            ->setDescription('HarrisStreet: Import and update Core_Config_Data settings for an environment');
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

        $this->_importerInstance = $this->_getFormatClass('Importer');

        $this->_setFolder($input->getArgument('folder'));
        $this->_setEnvironment($input->getArgument('env'));

        $baseFiles = $this->_getConfigurationBaseFiles();
        $envFiles  = $this->_getConfigurationEnvFiles();

        var_dump([$this->_folder, $this->_environment, $baseFiles, $envFiles]);
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

    /**
     * @param $folder
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function _setFolder($folder)
    {
        if (FALSE === is_dir($folder) || FALSE === is_readable($folder)) {
            throw new \InvalidArgumentException('Cannot access folder: ' . $folder);
        }
        $this->_folder = rtrim($folder, '/');
        return $this;
    }

    /**
     * @param $env
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function _setEnvironment($env)
    {
        $ef = $this->_folder . DIRECTORY_SEPARATOR . $env;
        if (FALSE === is_dir($ef) || FALSE === is_readable($ef)) {
            throw new \InvalidArgumentException('Cannot access folders for environment: ' . $env);
        }
        $this->_environment = explode(DIRECTORY_SEPARATOR, trim($env, DIRECTORY_SEPARATOR));
        return $this;
    }

    /**
     * a file is a Symfony\Component\Finder\SplFileInfo
     *
     * @return Finder
     * @throws \InvalidArgumentException
     */
    protected function _getConfigurationBaseFiles()
    {
        $format = $this->_input->getOption('format');

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*.' . $format)
            ->followLinks()
            ->in($this->_folder . DIRECTORY_SEPARATOR . $this->_getBaseFolderName() . DIRECTORY_SEPARATOR);

        if (0 === $finder->count()) {
            throw new \InvalidArgumentException('No base files found for format: *.' . $format);
        }
        return $finder;
    }

    protected function _getConfigurationEnvFiles()
    {
        $format = $this->_input->getOption('format');
        $files  = glob($this->_folder . DIRECTORY_SEPARATOR . $this->_getBaseFolderName() . DIRECTORY_SEPARATOR . '*.' . $format);
        if (0 === count($files)) {
            throw new \InvalidArgumentException('No env files found for format: *.' . $format);
        }
        return $files;
    }

    protected function _getBaseFolderName()
    {
        return $this->_input->getOption('base');
    }
}