<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData\Importer;

class Ascii extends Csv
{
    /**
     * http://en.wikipedia.org/wiki/Unit_separator#Field_separators
     * 31 Unit Separator    CSV_ENCLOSED_BY
     * 30 Record Separator  CSV_SEPARATOR
     * 29 Group Separator   PHP_EOL
     * 28 File Separator
     *
     * @param string $fileName
     *
     * @return string
     */
    public function parse($fileName)
    {
        $csvIterator = $this->_getCsvIterator($fileName, chr(30), chr(31), ''); // bug cannot pass chr 29

        return $this->_normalize($this->_normalizeFile($csvIterator));
    }
}