<?php
namespace Examples\HiveTransformETL\Conf;

/**
 * Base conf object
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 *
 */
abstract class BaseConf implements \ArrayAccess, \Iterator, \Countable {
    /**
     * Container for configurations
     * @var array
     */
    protected $vars = array();
    
    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return isset($this->vars[$offset]);
    }
    
    /**
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->vars[$offset] : null;
    }
    
    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        $this->vars[$offset] = $value;
    }
    
    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        unset($this->vars[$offset]);
    }

    /**
     * 
     * @see Iterator::current()
     */
    public function current() {
        return current($this->vars);
    }
    
    /**
     * 
     * @see Iterator::key()
     */
    public function key() {
        return key($this->vars);
    }
    
    /**
     * 
     * @see Iterator::next()
     */
    public function next() {
        return next($this->vars);
    }
    
    /**
     * 
     * @see Iterator::rewind()
     */
    public function rewind() {
        return reset($this->vars);
    }
    
    /**
     * 
     * @see Iterator::valid()
     */
    public function valid() {
        return !is_null($this->key());
    }
    
    /**
     * @see Countable::count()
     */
    public function count() {
        return count($this->vars);
    }
}