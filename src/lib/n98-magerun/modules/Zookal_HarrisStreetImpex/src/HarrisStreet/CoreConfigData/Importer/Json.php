<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData\Importer;

class Json extends AbstractImporter
{
    /**
     * Detects hierarchical structure so even a mixed file is possible
     *
     * @param string $fileName
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function parse($fileName)
    {
        $content = json_decode(file_get_contents($fileName), TRUE);

        if (0 !== json_last_error()) {
            throw new \InvalidArgumentException('Could not parse JSON file: ' . $fileName . '. ' . json_last_error_msg());
        }

        return $this->_normalize($content);
    }
}