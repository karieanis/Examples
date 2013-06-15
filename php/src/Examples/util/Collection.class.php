<?php
namespace Examples\Util;

/**
 * Basic collection implementation.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class Collection implements \Iterator, \Countable {
    /**
     * An array of collection
     * @var array
     */
    protected $collection;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->applyDefaultValues();
    }
    
    /**
     * 
     * @see Countable::count()
     */
    public function count() {
        return count($this->collection);
    }
    
    /**
     * 
     * @see Iterator::rewind()
     */
    public function rewind() {
        reset($this->collection);
    }
    
    /**
     * 
     * @see Iterator::current()
     */
    public function current() {
        return current($this->collection);
    }
    
    /**
     * 
     * @see Iterator::key()
     */
    public function key() {
        return key($this->collection);
    }
    
    /**
     * 
     * @see Iterator::next()
     */
    public function next() {
        next($this->collection);
    }
    
    /**
     * 
     * @see Iterator::valid()
     */
    public function valid() {
        return false !== $this->current();
    }
    
    public function clear() {
        $this->applyDefaultValues();
    }
    
    /**
     * Get all the items contained within this collection
     * @return array	An array of items
     */
    public function get() {
        return $this->collection;
    }
    
    /**
     * Get the item located at the passed position
     * @param mixed $pos
     * @return mixed
     */
    public function getAt($pos) {
        return isset($this->collection[$pos]) ? $this->collection[$pos] : null;
    }
    
    /**
     * Add the passed item to the stack
     * @param mixed $item
     * @return Collection
     */
    public function add($item) {
        array_push($this->collection, $item);
        return $this;
    }
    
    /**
     * Add a item to the stack at the passed position
     * @param mixed $item
     * @param mixed $pos
     * @return Collection
     */
    public function addAt($item, $pos) {
        if(is_numeric($pos)) {
            array_splice($this->collection, $pos, 0, $item);
        } else {
            $this->collection[$pos] = $item;    
        }
        
        return $this;
    }
    
    /**
     * Set the collection for this instance
     * @param array $collection
     * @return Collection
     */
    protected function set(array $collection) {
        $this->collection = $collection;
        return $this;
    }
    
    /**
     * Ensure that the default values are applied
     * @return void
     */
    protected function applyDefaultValues() {
        $this->set(array());
    }
}