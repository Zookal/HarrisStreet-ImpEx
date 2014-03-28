<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData;

abstract class AbstractImpexFileExtension
{
    private $_fileNameExtension = '';

    /**
     * @return string
     */
    public function getFileNameExtension()
    {
        if (TRUE === empty($this->_fileNameExtension)) {
            $class = explode('\\', get_class($this));
            return strtolower(end($class));
        }
        return $this->_fileNameExtension;
    }

    /**
     * @param string $fileNameExtension
     *
     * @return $this
     */
    public function setFileNameExtension($fileNameExtension)
    {
        $this->_fileNameExtension = $fileNameExtension;
        return $this;
    }
}