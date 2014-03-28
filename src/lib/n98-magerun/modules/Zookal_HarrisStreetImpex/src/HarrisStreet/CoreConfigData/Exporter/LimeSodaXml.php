<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 * @see         https://github.com/LimeSoda/LimeSoda_EnvironmentConfiguration
 */
class LimeSodaXml extends AbstractExporter
{

    public function __construct()
    {
        $this->setFileNameExtension('xml');
    }

    public function getData()
    {
        $xml         = new \Varien_Simplexml_Element('<config></config>');
        $defaultNode = $xml->addChild('global')->addChild('limesoda')->addChild('environments')->addChild('default');

        foreach ($this->_collection as $item) {
            /** @var $item \Mage_Core_Model_Config_Data */
            $defaultNode->addChild($this->_getNodeName($item), $this->_getNodeValue($item));
        }

        return $xml->asXML();
    }

    /**
     * @param \Mage_Core_Model_Config_Data $item
     *
     * @return string
     */
    protected function _getNodeName(\Mage_Core_Model_Config_Data $item)
    {
        $paths = explode('/', $item->getPath());
        foreach ($paths as &$path) {
            $path = ucfirst($path);
        }
        return implode('', $paths);
    }

    /**
     * @todo cdata export
     *
     * @param \Mage_Core_Model_Config_Data $item
     *
     * @return string
     */
    protected function _getNodeValue(\Mage_Core_Model_Config_Data $item)
    {
        $value  = $this->_multiLineToSingleLine($item->getValue());
        $return = 'config:set --scope=' . $item->getScope() . ' --scope_id=' . $item->getScopeId() . ' "' . $item->getPath() . '" "' . $value . '"';

        return $return;
    }
}