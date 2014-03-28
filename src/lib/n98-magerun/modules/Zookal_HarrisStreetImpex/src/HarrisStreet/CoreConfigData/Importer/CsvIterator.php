<?php
/**
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright   2014-present Zookal Pty Ltd, Sydney, Australia
 * @author      Cyrill at Schumacher dot fm [@SchumacherFM]
 */

namespace HarrisStreet\CoreConfigData\Importer;

class CsvIterator implements \Iterator
{
    const ROW_SIZE = 4096;

    /**
     * The pointer to the cvs file.
     * @var resource
     */
    private $_filePointer = NULL;

    /**
     * The current element, which will
     * be returned on each iteration.
     * @var array
     */
    private $_currentElement = NULL;

    /**
     * The row counter.
     * @var int
     */
    private $_rowCounter = NULL;

    /**
     * The delimiter for the csv file.
     * @var string
     */
    private $_delimiter = NULL;

    /**
     * @var string
     */
    private $_enclosure = NULL;

    /**
     * @var string
     */
    private $_escape = NULL;

    /**
     * @param string $file
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     *
     * @throws \Exception
     */
    public function __construct($file, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        try {
            $this->_filePointer = fopen($file, 'r');
            $this->_delimiter   = $delimiter;
            $this->_enclosure   = $enclosure;
            $this->_escape      = $escape;
        } catch (\Exception $e) {
            throw new \Exception('The file "' . $file . '" cannot be read.');
        }
    }

    /**
     * This method resets the file pointer.
     */
    public function rewind()
    {
        $this->_rowCounter = 0;
        rewind($this->_filePointer);
    }

    /**
     * This method returns the current csv row as a 2 dimensional array
     *
     * @return array The current csv row as a 2 dimensional array
     */
    public function current()
    {
        $this->_currentElement = fgetcsv($this->_filePointer, self::ROW_SIZE, $this->_delimiter);
        $this->_rowCounter++;
        return $this->_currentElement;
    }

    /**
     * This method returns the current row number.
     *
     * @return int The current row number
     */
    public function key()
    {
        return $this->_rowCounter;
    }

    /**
     * This method checks if the end of file is reached.
     *
     * @return boolean Returns true on EOF reached, false otherwise.
     */
    public function next()
    {
        return !feof($this->_filePointer);
    }

    /**
     * This method checks if the next row is a valid row.
     *
     * @return boolean If the next row is a valid row.
     */
    public function valid()
    {
        if (!$this->next()) {
            fclose($this->_filePointer);
            return FALSE;
        }
        return TRUE;
    }
}