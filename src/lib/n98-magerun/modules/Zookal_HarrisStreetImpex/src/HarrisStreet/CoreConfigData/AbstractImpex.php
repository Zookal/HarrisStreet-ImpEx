<?php

namespace HarrisStreet\CoreConfigData;

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractImpex extends AbstractMagentoCommand
{
    /**
     * @var InputInterface
     */
    protected $_input = NULL;

    /**
     * @var OutputInterface
     */
    protected $_output = NULL;

    protected function configure()
    {
        $this
            ->setName('hs:ccd:xxx')
            ->addOption('format', NULL, InputOption::VALUE_OPTIONAL, 'Format: yaml,json,csv,xml,limeSodaXml', 'yaml');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input  = $input;
        $this->_output = $output;
        $this->detectMagento($this->_output, TRUE);
        if (FALSE === $this->initMagento()) {
            throw new \RuntimeException('Magento could not be loaded');
        }
    }

    /**
     * @return \Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getExportCollection()
    {
        /** @var \Mage_Core_Model_Resource_Config_Data_Collection $collection */
        $collection = \Mage::getModel('core/config_data')->getCollection();
        $collection->setOrder('path', 'asc')->setOrder('scope', 'asc')->setOrder('scope_id', 'asc');

        $include = $this->_input->getOption('include');
        if (!empty($include) && is_string($include) === TRUE) {
            $includes = explode(',', $include);
            $orWhere  = array();
            foreach ($includes as $singlePath) {
                $singlePath = trim($singlePath);
                if (!empty($singlePath)) {
                    $orWhere[] = $collection->getConnection()->quoteInto('`path` like ?', $singlePath . '%');
                }
            }
            if (count($orWhere) > 0) {
                $collection->getSelect()->where(implode(' or ', $orWhere));
            }
        }

        $exclude = $this->_input->getOption('exclude');
        if (!empty($exclude) && is_string($exclude) === TRUE) {
            $excludes = explode(',', $exclude);
            $select   = $collection->getSelect();
            foreach ($excludes as $singleExclude) {
                $singleExclude = trim($singleExclude);
                if (!empty($singleExclude)) {
                    $select->Where('`path` not like ?', $singleExclude . '%');
                }
            }
        }
        // remove the id field and sort columns a-z
        foreach ($collection as $item) {
            /** @var $item \Mage_Core_Model_Config_Data */
            $data = $item->getData();
            unset($data['config_id']);
            ksort($data);
            $item->setData($data);
        }
        return $collection;
    }
}