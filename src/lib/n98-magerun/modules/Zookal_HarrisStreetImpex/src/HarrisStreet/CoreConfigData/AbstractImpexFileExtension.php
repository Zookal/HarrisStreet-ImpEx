<?php

namespace HarrisStreet\CoreConfigData;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
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