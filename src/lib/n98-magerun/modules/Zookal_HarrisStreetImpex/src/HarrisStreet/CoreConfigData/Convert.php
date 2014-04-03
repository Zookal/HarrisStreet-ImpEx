<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Convert extends Import
{
    /**
     * @var null|string
     */
    protected $_exportFileName = null;

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('hs:ccd:convert')
            ->addOption('export-file', null, InputOption::VALUE_OPTIONAL, 'File name in which the n98 commands shoud be written. If empty -> stdout')
            ->setDescription('HarrisStreet: Converts your configuration from a file format into a .magerun format for later processing via run "script".');

        $help = <<<HELP
Converts your configuration files into a n98 script command.
HELP;

        $this->setHelp($help);
    }

    protected function processImport()
    {
        $this->_exportFileName = trim($this->_input->getOption('export-file'));
        $this->_exportFileName = empty($this->_exportFileName) ? null : $this->_exportFileName;
        if (null !== $this->_exportFileName && false === touch($this->_exportFileName)) {
            throw new \InvalidArgumentException('Cannot create and write to file: ' . $this->_exportFileName);
        }
        parent::processImport();
    }

    /**
     * @param string $command
     *
     * @return int
     */
    protected function processCommand($command)
    {
        $output = $command . PHP_EOL;
        if (null === $this->_exportFileName) {
            echo $output;
        } else {
            file_put_contents($this->_exportFileName, $output, FILE_APPEND);
        }

        return 0;
    }

    /**
     * @param string $file
     * @param int    $valuesSet
     */
    protected function infoOutPut($file, $valuesSet = 0)
    {
        if (null !== $this->_exportFileName) {
            parent::infoOutPut($file, $valuesSet);
        }
    }
}