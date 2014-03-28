<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

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
