<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Xml extends AbstractExporter
{
    const SLASH_REPLACEMENT = '__';

    /**
     * @var XmlElement
     */
    protected $_xml = NULL;

    /**
     * @return string
     */
    public function getData()
    {
        if ($this->getIsHierarchical()) {
            return $this->_getHierarchicalData();
        }
        return $this->_getFlatData();
    }

    /**
     * @return string
     */
    protected function _getFlatData()
    {
        $this->_xml = new XmlElement('<config/>');

        foreach ($this->_collection as $item) {
            /** @var $item \Mage_Core_Model_Config_Data */

            $path = str_replace('/', self::SLASH_REPLACEMENT, $item->getPath());
            if (isset($this->_xml->$path)) {
                $nodePath = $this->_xml->$path;
            } else {
                $nodePath = $this->_xml->addChild($path);
            }

            if (isset($nodePath->{$item->getScope()})) {
                $nodeScope = $nodePath->{$item->getScope()};
            } else {
                $nodeScope = $nodePath->addChild($item->getScope());
            }

            $valueChild = $nodeScope->addChild('value', $item->getValue());
            $valueChild->addAttribute('scope_id', $item->getScopeId());
        }

        return $this->_xml->asNiceXml();
    }

    /**
     * @return string
     */
    protected function _getHierarchicalData()
    {
        $this->_xml = new XmlElement('<config/>');

        foreach ($this->_collection as $item) {
            /** @var $item \Mage_Core_Model_Config_Data */

            $path           = $item->getPath() . '/' . $item->getScope();
            $nodePathValues = $this->_xml->setNode($path);

            $valueChild = $nodePathValues->addChild('value', $item->getValue());
            $valueChild->addAttribute('scope_id', $item->getScopeId());
        }

        return $this->_xml->asNiceXml();
    }
}