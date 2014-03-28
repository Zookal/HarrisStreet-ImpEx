<?php

namespace HarrisStreet\CoreConfigData\Exporter;

use Symfony\Component\Yaml\Dumper;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Yaml extends AbstractExporter
{
    public function getData()
    {

        if (TRUE === $this->getIsHierarchical()) {
            $yaml = new Dumper();
            $yaml->setIndentation(2);
            return $yaml->dump($this->_prepareHierarchicalCollection(), 5, 0, FALSE, TRUE);
        } else {
            $configSet = $this->_prepareFlatCollection();
            return $this->_generateYaml($configSet);
        }
    }

    /**
     * Custom format with nice headers only for flat structure available
     *
     * @param array $data
     *
     * @return string
     */
    protected function _generateYaml(array $data)
    {
        $fileContent = array();
        $header      = array();
        foreach ($data as $path => $scopes) {

            $paths = explode('/', $path);
            if (!isset($header[$paths[0]])) {
                $header[$paths[0]] = TRUE;
                $length            = strlen($paths[0]) + 2;
                $fileContent[]     = PHP_EOL . $this->_getRS($length, '#') . PHP_EOL . '# ' . $paths[0] . PHP_EOL . $this->_getRS($length, '#');
            }

            $fileContent[] = $path . ':';
            foreach ($scopes as $scope => $scopeValues) {
                $fileContent[] = $this->_getRS(2) . $scope . ':';
                foreach ($scopeValues as $scopeId => $value) {
                    $fileContent[] = $this->_getRS(4) . $scopeId . ': ' . $this->_prepareValue($value);
                }
            }
        }
        return implode(PHP_EOL, $fileContent);
    }

    /**
     * @param  int   $length
     * @param string $string
     *
     * @return string
     */
    protected function _getRS($length, $string = ' ')
    {
        return str_repeat($string, $length);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function _prepareValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        if (strpos($value, "\n") !== FALSE) {
            $values = explode("\n", $value);
            foreach ($values as &$line) {
                $line = $this->_getRS(8) . $line;
            }
            $value = implode("\n", $values);
            return "|\n" . $value;
        }

        return '\'' . addcslashes($value, '\'') . '\'';
    }
}