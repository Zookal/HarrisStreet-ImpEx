<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

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
     * Imports a bunch of files. The last imported file will always overwrite the settings from the previous one
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->_importerInstance = $this->_getFormatClass('Importer');

        if (FALSE === $this->_importerInstance) {
            throw new \InvalidArgumentException('No supported import format found!');
        }

        $this->_setFolder($input->getArgument('folder'));
        $this->_setEnvironment($input->getArgument('env'));

        $configFiles = array_merge($this->_getConfigurationBaseFiles(), $this->_getConfigurationEnvFiles());

        $this->getApplication()->setAutoExit(FALSE);

        foreach ($configFiles as $file) {
            $valuesSet      = 0;
            $configurations = $this->_importerInstance->parse($file);
            /**
             *  'catalog/downloadable/samples_title' =>     $path
             *   array(1) {                                 $config
             *     'default' =>
             *     array(1) {
             *       [0] =>
             *       string(7) "Samples"
             *     }
             *   }
             */
            foreach ($configurations as $path => $config) {
                $commands = $this->_getN98ConfigSets($path, $config);
                foreach ($commands as $command) {
                    $this->getApplication()->run(new StringInput($command), $output);
                    $valuesSet++;
                }
            }
            $this->_output->writeln('<info>Processed: ' . $file . ' with ' . $valuesSet . ' value' . ($valuesSet < 2 ? '' : 's') . '.</info>');
        }

        $this->getApplication()->setAutoExit(TRUE);
    }

    /**
     * @param string $path
     * @param array  $config
     *
     * @return array
     */
    protected function _getN98ConfigSets($path, array $config)
    {
        $return = array();
        foreach ($config as $scope => $scopeIdValue) {
            foreach ($scopeIdValue as $scopeId => $value) {
                $scopeId  = (int)$scopeId;
                $value    = str_replace("\r", '', addcslashes($value, '"'));
                $value    = str_replace("\n", '\\n', $value); // no multiline statements possible :-(
                $return[] = 'config:set --scope=' . $scope . ' --scope-id=' . $scopeId . ' "' . $path . '" "' . $value . '"';
            }
        }
        return $return;
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
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function _getConfigurationBaseFiles()
    {
        $files = $this->_find($this->_folder . DIRECTORY_SEPARATOR . $this->_getBaseFolderName() . DIRECTORY_SEPARATOR);
        if (0 === count($files)) {
            $extension = $this->_importerInstance->getFileNameExtension();
            throw new \InvalidArgumentException('No base files found for format: *.' . $extension);
        }
        return $files;
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function _getConfigurationEnvFiles()
    {

        $fullEnvPath = '';
        $files       = array();
        foreach ($this->_environment as $envPath) {
            $fullEnvPath .= $envPath . DIRECTORY_SEPARATOR;
            $find  = $this->_find($this->_folder . DIRECTORY_SEPARATOR . $fullEnvPath, '0');
            $files = array_merge($files, $find);
        }

        if (0 === count($files)) {
            $extension = $this->_importerInstance->getFileNameExtension();
            throw new \InvalidArgumentException('No env files found for format: *.' . $extension);
        }
        return $files;
    }

    /**
     * @param string $path
     * @param null   $depth
     *
     * @return array
     */
    protected function _find($path, $depth = NULL)
    {
        $extension = $this->_importerInstance->getFileNameExtension();
        $finder    = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*.' . $extension)
            ->followLinks()
            ->in($path);

        if (NULL !== $depth) {
            $finder->depth($depth);
        }

        $files = array();
        foreach ($finder as $file) {
            /** @var $file \Symfony\Component\Finder\SplFileInfo */
            $files[] = $file->getPathname();
        }
        return $files;
    }

    protected function _getBaseFolderName()
    {
        return $this->_input->getOption('base');
    }
}