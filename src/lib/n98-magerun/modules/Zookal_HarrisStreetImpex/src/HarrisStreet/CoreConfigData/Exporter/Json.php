<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Json extends AbstractExporter
{

    public function getData()
    {
        $data = $this->_prepareCollection();
        return json_encode($data, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
    }

    /**
     * Hmmm
     *
     * @return array
     * @throws \Exception
     */
    protected function _prepareCollection()
    {
        $return = array();
        foreach ($this->_collection as $row) {
            /** @var $row \Mage_Core_Model_Config_Data */

            $pathDetails = explode('/', $row->getPath());

            if (!isset($return[$pathDetails[0]])) {
                $return[$pathDetails[0]] = array();
            }
            if (!isset($return[$pathDetails[0]][$pathDetails[1]])) {
                $return[$pathDetails[0]][$pathDetails[1]] = array();
            }
            if (!isset($return[$pathDetails[0]][$pathDetails[1]][$pathDetails[2]])) {
                $return[$pathDetails[0]][$pathDetails[1]][$pathDetails[2]] = array();
            }
            if (!isset($return[$pathDetails[0]][$pathDetails[1]][$pathDetails[2]][$row->getScope()])) {
                $return[$pathDetails[0]][$pathDetails[1]][$pathDetails[2]][$row->getScope()] = array();
            }

            $return[$pathDetails[0]][$pathDetails[1]][$pathDetails[2]][$row->getScope()]['' . $row->getScopeId()] = $row->getValue();
        }

        return $return;
    }
}