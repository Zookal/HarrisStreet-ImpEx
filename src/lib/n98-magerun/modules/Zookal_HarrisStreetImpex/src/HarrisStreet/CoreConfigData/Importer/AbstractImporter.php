<?php

namespace HarrisStreet\CoreConfigData\Importer;

use HarrisStreet\CoreConfigData\AbstractImpexFileExtension;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
abstract class AbstractImporter extends AbstractImpexFileExtension implements ImporterInterface
{

    private $_isHierarchical = FALSE;

    /**
     * @param bool $isHierarchical
     *
     * @return $this
     */
    public function setIsHierarchical($isHierarchical)
    {
        $this->_isHierarchical = (boolean)$isHierarchical;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsHierarchical()
    {
        return $this->_isHierarchical;
    }
}