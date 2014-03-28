<?php
namespace HarrisStreet\CoreConfigData\Importer;

interface ImporterInterface
{

    /**
     * @param string $fileName
     *
     * @return array
     */
    public function parse($fileName);

    /**
     * @param boolean $isHierarchical
     *
     * @return $this
     */
    public function setIsHierarchical($isHierarchical);

    public function getIsHierarchical();
}
