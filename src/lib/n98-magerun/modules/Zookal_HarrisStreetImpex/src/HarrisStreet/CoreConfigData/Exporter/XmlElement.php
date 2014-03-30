<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

class XmlElement extends \Varien_Simplexml_Element
{
    /**
     * @param      $path
     * @param      $value
     *
     * @return $this|XmlElement|\SimpleXMLElement|\SimpleXMLElement[]
     */
    public function setNode($path, $value = NULL)
    {
        $arr1 = explode('/', $path);
        $arr  = array();
        foreach ($arr1 as $v) {
            if (!empty($v)) $arr[] = $v;
        }
        $last = count($arr) - 1;
        $node = $this;
        foreach ($arr as $i => $nodeName) {
            if ($last === $i && NULL !== $value) {
                if (!isset($node->$nodeName) || $overwrite) {
                    $node->$nodeName = $value;
                }
            } else {
                if (!isset($node->$nodeName)) {
                    $node = $node->addChild($nodeName);
                } else {
                    $node = $node->$nodeName;
                }
            }
        }
        return $node;
    }
}
