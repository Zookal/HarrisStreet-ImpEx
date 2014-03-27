<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Ascii extends AbstractExporter
{

    /**
     * This data cannot be edited in a text editor except you can show special characters
     *
     * @return string
     */
    public function getData()
    {
        $fileContent = array();
        $i           = 0;
        foreach ($this->_collection as $item) {
            /** @var $item \Mage_Core_Model_Config_Data */
            if (0 === $i) {
                $fileContent[] = $this->_getRow(array_keys($item->getData()));
            }

            $fileContent[] = $this->_getRow($item->getData());
            $i++;
        }
        return implode(chr(29), $fileContent);
    }

    /**
     * http://en.wikipedia.org/wiki/Unit_separator#Field_separators
     * 31 Unit Separator    CSV_ENCLOSED_BY
     * 30 Record Separator  CSV_SEPARATOR
     * 29 Group Separator   PHP_EOL
     * 28 File Separator
     *
     * @param array $values
     *
     * @return string
     */
    protected function _getRow(array $values)
    {
        return chr(31) . implode(chr(31) . chr(30) . chr(31), $values) . chr(31);
    }
}