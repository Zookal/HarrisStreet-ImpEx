<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Csv extends AbstractExporter
{
    const CSV_SEPARATOR   = ';';
    const CSV_ENCLOSED_BY = '"';

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
        return implode(PHP_EOL, $fileContent);
    }

    protected function _getRow(array $values)
    {
        foreach ($values as &$v) {
            $v = $this->_multiLineToSingleLine($v);
        }

        return self::CSV_ENCLOSED_BY .
        implode(self::CSV_ENCLOSED_BY . self::CSV_SEPARATOR . self::CSV_ENCLOSED_BY, $values) .
        self::CSV_ENCLOSED_BY;
    }
}