<?php
namespace Examples\Hive;

/**
 * Result set object used for interrogating  data from HiveServer2 result sets
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HivePDOResultSet implements \Iterator, \Countable {
    /**
     * The underlying rows within this result set
     * @var array
     */
    protected $rows;
    
    /**
     * A metadata map of columns to value property accessors
     * @var Meta\PropertyMap
     */
    protected $propertyMap;
    /**
     * A metadata map of columns to column names
     * @var Meta\KeyMap
     */
    protected $keyMap;

    /**
     * Constructor
     * @param array $rows
     */
    public function __construct(array $rows = array()) {
        $this->setRows($rows);
    }
    
    /**
     * Get the current row, then advance the pointer to the next row
     * @return mixed
     */
    public function getRow() {
        $row = $this->_getRow();
        $this->next();

        return $row;
    }
    
    /**
     * @see Iterator::current()
     * @codeCoverageIgnore
     */
    public function current() {
        return $this->_getRow();
    }
    
    /**
     * @see Iterator::key()
     * @codeCoverageIgnore
     */
    public function key() {
        return key($this->rows);
    }
    
    /**
     * @see Iterator::next()
     * @codeCoverageIgnore
     */
    public function next() {
        next($this->rows);
        return $this->_getRow();
    }
    
    /**
     * @see Iterator::rewind()
     * @codeCoverageIgnore
     */
    public function rewind() {
        reset($this->rows);
        return $this->_getRow();
    }
    
    /**
     * @see Iterator::valid()
     * @codeCoverageIgnore
     */
    public function valid() {
        return !is_null($this->key());
    }
    
    /**
     * @see Countable::count()
     * @codeCoverageIgnore
     */
    public function count() {
        return count($this->rows);
    }
    
    /**
     * Check if this result set is empty
     * @return boolean
     * @codeCoverageIgnore
     */
    public function isEmpty() {
        return $this->count() === 0;
    }
    
    /**
     * Traverse the columns for the current row of the underlying HiveServer2 result set. Use the metadata objects
     * to populate key / values within the out row. Will return false if this is the end of the HiveServer2 result set.
     * @return array|false
     */
    protected function _getRow() {
        $outRow = array();
        
        $keys = $this->getKeyMap()->get();
        $properties = $this->getPropertyMap()->get();

        /* @var \TRow $row */
        if($row = current($this->rows)) {
            /* @var \TColumnValue $col */
            foreach($row->colVals as $pos => $col) {
                $outRow[$keys[$pos]] = $col->{$properties[$pos]}->value;  
            }
        } else {
            $outRow = false;
        }
        
        return $outRow;
    }
    
    /**
     * Set the rows for this result set
     * @param array $rows
     * @return \Examples\Hive\HivePDOResultSet
     */
    public function setRows(array $rows) {
        $this->rows = $rows;
        return $this;
    }
    
    /**
     * Get the key metadata
     * @return \Meta\KeyMap
     */
    public function getKeyMap() {
        return $this->keyMap;
    }
    
    /**
     * Set the key meta data
     * @param Meta\KeyMap $map
     * @return \Examples\Hive\HivePDOResultSet
     */
    public function setKeyMap(Meta\KeyMap $map) {
        $this->keyMap = $map;
        return $this;
    }
    
    /**
     * Get the value property accessor metadata
     * @return \Meta\PropertyMap
     */
    public function getPropertyMap() {
        return $this->propertyMap;
    }
    
    /**
     * Set the value property accessor metadata
     * @param Meta\PropertyMap $map
     * @return \Examples\Hive\HivePDOResultSet
     */
    public function setPropertyMap(Meta\PropertyMap $map) {
        $this->propertyMap = $map;
        return $this;
    }
}