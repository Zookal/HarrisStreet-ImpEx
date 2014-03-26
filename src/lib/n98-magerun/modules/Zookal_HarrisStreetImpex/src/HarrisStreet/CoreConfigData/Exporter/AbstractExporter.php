<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
abstract class AbstractExporter implements ExporterInterface
{
    /**
     * @var \Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected $_collection = NULL;

    /**
     * Run script
     *
     */
    public function setData(\Mage_Core_Model_Resource_Config_Data_Collection $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function _multiLineToSingleLine($str)
    {
        $str = str_replace(array("\r\n", "\n"), '\\n', $str);
        return addcslashes($str, '"');
    }
}