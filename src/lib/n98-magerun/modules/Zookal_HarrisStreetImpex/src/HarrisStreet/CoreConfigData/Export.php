<?php

namespace HarrisStreet\CoreConfigData;

use HarrisStreet\CoreConfigData\Exporter\ExporterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class Export extends AbstractImpex
{
    /**
     * @var ExporterInterface
     */
    protected $_exporterInstance = NULL;

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('hs:ccd:export')
            ->addOption('filename', NULL, InputOption::VALUE_OPTIONAL, 'File name into which should the export be written. Defaults into var directory.')
            ->addOption('include', NULL, InputOption::VALUE_OPTIONAL, 'Path prefix, multiple values can be comma separated; exports only those paths')
            ->addOption('exclude', NULL, InputOption::VALUE_OPTIONAL, 'Path prefix, multiple values can be comma separated; exports everything except ...')
            ->addOption('filePerNameSpace', NULL, InputOption::VALUE_OPTIONAL,
                'Export each namespace into its own file. Set to: yes', 'no')
            ->addOption('exclude-default', NULL, InputOption::VALUE_OPTIONAL, 'Excludes default values (@todo)')
            ->setDescription('HarrisStreet: Exports Core_Config_Data settings into a file.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->exporterInstance = $this->_getFormatClass();
        if (FALSE === $this->_exporterInstance) {
            throw new \InvalidArgumentException('Not supported export format!');
        }

        if ('yes' === $input->getOption('filePerNameSpace')) {
            return $this->_createMultipleFiles();
        }

        return $this->_createSingleFile();
    }

    /**
     * Exports one file per namespace
     *
     * @return int
     */
    protected function _createMultipleFiles()
    {

        $this->_output->writeln('<info>Wrote: nothing</info>');
        return 0;
    }

    /**
     * Exports everything in one file
     *
     * @return int
     */
    protected function _createSingleFile()
    {
        $collection = $this->_getExportCollection();
        $this->_exporterInstance->setData($collection);

        $fileName = $this->_getFileName();
        $written  = file_put_contents($fileName, $this->_exporterInstance->getData());
        if (FALSE === $written) {
            $this->_output->writeln('<error>Failed to write: ' . $fileName . '</error>');
            return 1;
        }
        $this->_output->writeln('<info>Wrote: ' . $collection->count() . ' settings to file ' . $fileName . '</info>');
        return 0;
    }

    /**
     * @return bool|ExporterInterface
     */
    protected function _getFormatClass()
    {
        $format      = $this->_input->getOption('format');
        $class       = ucfirst($format);
        $classPrefix = 'HarrisStreet\\CoreConfigData\\Exporter\\';

        if (TRUE === class_exists($classPrefix . $class, TRUE)) {
            $interfaces = class_implements($classPrefix . $class);
            if (isset($interfaces[$classPrefix . 'ExporterInterface'])) {
                $c = $classPrefix . $class;
                return new $c();
            }
        }
        return FALSE;
    }

    /**
     *
     * @return string
     */
    protected function _getFileName()
    {
        $fileName = $this->_input->getOption('filename');
        if (FALSE === empty($fileName)) {
            return $fileName;
        }

        $ext = '' === $this->_exporterInstance->getFileNameExtension()
            ? $this->_input->getOption('format')
            : $exporterInstance->getFileNameExtension();
        return \Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'config_' . date('Ymd_His') . '.' . $ext;
    }
}