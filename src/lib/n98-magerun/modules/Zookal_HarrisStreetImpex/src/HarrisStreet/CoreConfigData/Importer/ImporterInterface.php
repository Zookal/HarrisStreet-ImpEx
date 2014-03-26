<?php
namespace HarrisStreet\CoreConfigData\Exporter;

interface ImporterInterface
{

    /**
     * @param \Mage_Core_Model_Resource_Config_Data_Collection $collection
     *
     * @return $this
     */
    public function setData(\Mage_Core_Model_Resource_Config_Data_Collection $collection);

    public function import();
}
