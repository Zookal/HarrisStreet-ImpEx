<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData\Importer;

class Csv extends AbstractImporter
{
    /**
     * Every row which has not a slash in its path will be skipped
     *
     * array(4) {
     *  [0] =>
     *  string(4) "path"
     *  [1] =>
     *  string(5) "scope"
     *  [2] =>
     *  string(8) "scope_id"
     *  [3] =>
     *  string(5) "value"
     * }
     *
     * @param string $fileName
     *
     * @return array
     * @throws \Exception
     */
    public function parse($fileName)
    {
        $csvIterator = $this->_getCsvIterator($fileName, ';');

        return $this->_normalize($this->_normalizeFile($csvIterator));
    }

    /**
     * @param CsvIterator $csvIterator
     *
     * @return array
     */
    protected function _normalizeFile(CsvIterator $csvIterator)
    {

        /**
         *  'catalog/downloadable/samples_title' =>     $path
         *   array(1) {                                 $config
         *     'default' =>
         *     array(1) {
         *       [0] =>
         *       string(7) "Samples"
         *     }
         *   }
         */
        $content = array();
        foreach ($csvIterator as $row) {
            if (FALSE === strpos($row[0], '/')) {
                continue;
            }

            if (!isset($content[$row[0]])) {
                $content[$row[0]] = array();
            }
            if (!isset($content[$row[0]][$row[1]])) {
                $content[$row[0]][$row[1]] = array();
            }
            $content[$row[0]][$row[1]][$row[2]] = $row[3];
        }
        return $content;
    }

    /**
     * @param  string $fileName
     * @param string  $delimiter
     * @param string  $enclosure
     * @param string  $escape
     *
     * @return CsvIterator
     */
    protected function _getCsvIterator($fileName, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        return new CsvIterator($fileName, $delimiter, $enclosure, $escape);
    }
}