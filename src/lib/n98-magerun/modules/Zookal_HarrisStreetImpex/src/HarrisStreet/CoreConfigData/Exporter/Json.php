<?php

namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
class Json extends AbstractExporter
{

    public function getData()
    {
        return json_encode($this->_prepareCollection(), JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
    }
}