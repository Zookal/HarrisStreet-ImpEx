<?php
namespace HarrisStreet\CoreConfigData\Importer;

interface ImporterInterface
{

    /**
     * @param \Varien_Data_Collection $collection
     *
     * @return $this
     */
    public function setData(\Varien_Data_Collection $collection);

    public function import();

    /**
     * @param boolean $isHierarchical
     *
     * @return $this
     */
    public function setIsHierarchical($isHierarchical);

    public function getIsHierarchical();
}
