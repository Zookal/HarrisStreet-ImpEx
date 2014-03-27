<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Json2 extends AbstractExporter
{
    protected $_fileNameExtension = 'json';

    public function getData()
    {
        $data = $this->_prepareCollection();
        return json_encode($data, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _prepareCollection()
    {
        $return = array();
        foreach ($this->_collection as $row) {
            /** @var $row \Mage_Core_Model_Config_Data */

            if (!isset($return[$row->getPath()])) {
                $return[$row->getPath()] = array();
            }
            if (!isset($return[$row->getPath()][$row->getScope()])) {
                $return[$row->getPath()][$row->getScope()] = array();
            }

            $return[$row->getPath()][$row->getScope()]['' . $row->getScopeId()] = $row->getValue();
        }

        return $return;
    }
}