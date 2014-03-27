<?php

namespace HarrisStreet\CoreConfigData\Importer;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
abstract class AbstractImporter implements ImporterInterface
{

    private $_isHierarchical = FALSE;

    /**
     * @var \Varien_Data_Collection
     */
    protected $_collection = NULL;

    /**
     * Run script
     *
     */
    public function setData(\Varien_Data_Collection $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

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