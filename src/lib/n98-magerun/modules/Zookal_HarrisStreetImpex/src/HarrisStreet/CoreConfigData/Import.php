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
     * @type string
     */
    const DELETE_VALUE = '!!delete';
    
    /**
     * @var string
     */
    protected $_folder = null;

    /**
     * @var array
     */
    protected $_environment = null;

    /**
     * @var ImporterInterface
     */
    protected $_importerInstance = null;

    /** @var \Mage_Core_Model_Resource_Website_Collection  */
    protected $_websites = null;

    /** @var \Mage_Core_Model_Resource_Store_Collection  */
    protected $_stores = null;

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('hs:ccd:import')
            ->addArgument('folder', InputArgument::REQUIRED, 'Import folder name')
            ->addArgument('env', InputArgument::REQUIRED, 'Environment name. SubEnvs separated by slash e.g.: development/osx/developer01')
            ->addOption('base', null, InputOption::VALUE_OPTIONAL, 'Base folder name', 'base')
            ->setDescription('HarrisStreet: Import and update Core_Config_Data settings for an environment');
    }

    /**
     * Imports a bunch of files. The last imported file will always overwrite the settings from the previous one.
     * Environment folder names can have a hierarchical structure.
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
        $this->processImport();
    }

    /**
     * Runs the importer
     *
     * @throws \InvalidArgumentException
     */
    protected function processImport()
    {
        $this->_importerInstance = $this->_getFormatClass('Importer');

        if (false === $this->_importerInstance) {
            throw new \InvalidArgumentException('No supported import format found!');
        }

        $this->_setFolder($this->_input->getArgument('folder'));
        $this->_setEnvironment($this->_input->getArgument('env'));

        $this->getApplication()->setAutoExit(false);

        foreach ($this->_getConfigurationFiles() as $file) {
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
                if ($path == 'commands/run') {
                    $commands = $config;
                } else {
                    $commands = $this->_getN98ConfigSets($path, $config);
                }

                foreach ($commands as $command) {
                    $this->processCommand($command);
                    $valuesSet++;
                }
            }
            $this->infoOutPut($file, $valuesSet);
        }

        $this->getApplication()->setAutoExit(true);
    }

    /**
     * @param string $file
     * @param int    $valuesSet
     */
    protected function infoOutPut($file, $valuesSet = 0)
    {
        $this->_output->writeln('<info>Processed: ' . $file . ' with ' . $valuesSet . ' value' . ($valuesSet < 2 ? '' : 's') . '.</info>');
    }

    /**
     * @param string $command
     *
     * @return int
     */
    protected function processCommand($command)
    {
        return $this->getApplication()->run(new StringInput($command), $this->_output);
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
                $scopeId = (int)$scopeId;
                $valid = $this->isValidScopeIdCombination($scope, $scopeId);
                if (!$valid) {
                    // Invalid scope write error to output
                    $msg = "<error>ERROR: invalid scopeId: $scopeId for scope $scope</error>\n";
                    $msg .= "<error>$path => $value</error>";
                    $this->_output->writeln($msg);
                    continue;
                }

                // Valid scope Write output
                $value = str_replace("\r", '', addcslashes($value, '"'));
                $value = str_replace("\n", '\\n', $value); // no multiline statements possible :-(
                
                if ($value === self::DELETE_VALUE) {
                    $return[] = 'config:delete --scope=' . $scope . ' --scope-id=' . $scopeId . ' "' . $path . '"';
                } else {
                    $return[] = 'config:set --scope=' . $scope . ' --scope-id=' . $scopeId . ' -- "' . $path . '" "' . $value . '"';
                }
            }
        }

        return $return;
    }


    /**
     * @param string $scope
     * @param int    $scopeId
     *
     * @return bool
     */
    protected function isValidScopeIdCombination($scope, $scopeId)
    {
        $valid = true;
        if ($scope === 'default') {
            // Default Store only valid for id 0
            if ($scopeId !== 0) {
                $valid = false;
            }
        }
        elseif ($scope === 'websites') {
            // Check if website with id exists
            $valid = $this->isValidWebsiteId($scopeId);
        }
        elseif ($scope === 'stores') {
            // Check if store with id exists
            $valid = $this->isValidStoreId($scopeId);
        }

        return $valid;
    }

    /**
     * @param int $websiteId
     *
     * @return bool
     */
    protected function isValidWebsiteId($websiteId)
    {
        $website = $this->getWebsites()->getItemById($websiteId);

        if ($website === null) {
            return false;
        }

        if (!$website->getId()) {
            return false;
        }

        return true;
    }

    /**
     * @param int $storeId
     *
     * @return bool
     */
    protected function isValidStoreId($storeId)
    {
        $store = $this->getStores()->getItemById($storeId) ;

        if ($store === null) {
            return false;
        }

        if (!$store->getId()) {
            return false;
        }

        return true;
    }

    /**
     * @param $folder
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function _setFolder($folder)
    {
        if (false === is_dir($folder) || false === is_readable($folder)) {
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
        if (false === is_dir($ef) || false === is_readable($ef)) {
            throw new \InvalidArgumentException('Cannot access folders for environment: ' . $env);
        }
        $this->_environment = explode(DIRECTORY_SEPARATOR, trim($env, DIRECTORY_SEPARATOR));
        return $this;
    }

    /**
     * @return array
     */
    protected function _getConfigurationFiles()
    {
        return array_merge($this->_getConfigurationBaseFiles(), $this->_getConfigurationEnvFiles());
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
    protected function _find($path, $depth = null)
    {
        $extension = $this->_importerInstance->getFileNameExtension();
        $finder    = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*.' . $extension)
            ->followLinks()
            ->in($path);

        if (null !== $depth) {
            $finder->depth($depth);
        }

        $files = array();
        foreach ($finder as $file) {
            /** @var $file \Symfony\Component\Finder\SplFileInfo */
            $files[] = $file->getPathname();
        }
        return $files;
    }

    /**
     * @return string
     */
    protected function _getBaseFolderName()
    {
        return $this->_input->getOption('base');
    }

    /**
     * @return \Mage_Core_Model_Resource_Website_Collection
     */
    public function getWebsites()
    {
        if ($this->_websites === null) {
            $this->_websites = \Mage::getResourceModel('core/website_collection');
        }

        return $this->_websites;
    }

    /**
     * @return \Mage_Core_Model_Resource_Store_Collection
     */
    public function getStores()
    {
        if ($this->_stores === null) {
            $this->_stores = \Mage::getResourceModel('core/store_collection');
        }

        return $this->_stores;
    }
}
