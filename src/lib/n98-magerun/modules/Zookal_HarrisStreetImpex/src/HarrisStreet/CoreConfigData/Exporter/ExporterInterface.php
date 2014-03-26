<?php
namespace HarrisStreet\CoreConfigData\Exporter;

interface ExporterInterface
{

    /**
     * @param \Mage_Core_Model_Resource_Config_Data_Collection $collection
     *
     * @return $this
     */
    public function setData(\Mage_Core_Model_Resource_Config_Data_Collection $collection);

    /**
     * @return string
     */
    public function getData();
}
