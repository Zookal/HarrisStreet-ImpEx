<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData\Importer;

use HarrisStreet\CoreConfigData\AbstractImpexFileExtension;

abstract class AbstractImporter extends AbstractImpexFileExtension implements ImporterInterface
{

    private $_isHierarchical = FALSE;

    /**
     * @param bool $isHierarchical
     *
     * @return $this
     */
    public function setIsHierarchical($isHierarchical)
    {
        $this->_isHierarchical = (boolean)$isHierarchical;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsHierarchical()
    {
        return $this->_isHierarchical;
    }

    protected function  _normalize(array $content)
    {
        $return = array();
        // try to detect hierarchical structure
        foreach ($content as $nameSpace => $settings) {
            if (strpos($nameSpace, '/') === FALSE) {
                $cfgValues = $this->_flatten($nameSpace, $settings);
                $return    = array_merge($return, $cfgValues);
            } else {
                $return[$nameSpace] = $settings;
            }
        }
        return $return;
    }

    /**
     * @param string $nameSpace1
     * @param array  $settings1
     *
     * @return array
     */
    protected function _flatten($nameSpace1, array $settings1)
    {

        $return = array();
        foreach ($settings1 as $nameSpace2 => $settings2) {
            foreach ($settings2 as $nameSpace3 => $settings3) {
                $return[$nameSpace1 . '/' . $nameSpace2 . '/' . $nameSpace3] = $settings3;
            }
        }

        return $return;
    }
}