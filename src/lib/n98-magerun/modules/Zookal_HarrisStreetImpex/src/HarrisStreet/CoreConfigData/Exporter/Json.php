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
        return json_encode($this->_collection->toArray(), JSON_PRETTY_PRINT);
    }
}