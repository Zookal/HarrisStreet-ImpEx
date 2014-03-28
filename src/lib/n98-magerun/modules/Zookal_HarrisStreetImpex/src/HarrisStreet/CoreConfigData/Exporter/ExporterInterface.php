<?php
namespace HarrisStreet\CoreConfigData\Exporter;

/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */
interface ExporterInterface
{

    /**
     * @param \Varien_Data_Collection $collection
     *
     * @return $this
     */
    public function setData(\Varien_Data_Collection $collection);

    /**
     * @return string
     */
    public function getData();

    public function getFileNameExtension();

    /**
     * @param boolean $isHierarchical
     *
     * @return $this
     */
    public function setIsHierarchical($isHierarchical);

    public function getIsHierarchical();
}
