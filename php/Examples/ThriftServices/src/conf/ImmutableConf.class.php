<?php
namespace Examples\ThriftServices\Conf;

/**
 * Wrapper class to enforce immutability on inner conf classes
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class ImmutableConf implements \ArrayAccess, \Iterator, \Countable {
    /**
     * @var BaseConf
     */
    protected $innerConf;
    
    /**
     * @param BaseConf $conf
     */
    public function __construct(BaseConf $conf) {
        $this->innerConf = $conf;
    }
    
    /* 
     * @codeCoverageIgnore
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return $this->innerConf->offsetExists($offset);
    }
    
    /* 
     * @codeCoverageIgnore
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        return $this->innerConf->offsetGet($offset);
    }
    
    /* 
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        throw new \Examples\ThriftServices\Exception\OperationNotAllowedException(
            "Immutable configuration does not support the setting of new values"
        );
    }
    
    /* 
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        throw new \Examples\ThriftServices\Exception\OperationNotAllowedException(
            "Immutable configuration does not support the unsetting of existing values"
        );
    }
    
    /* 
     * @codeCoverageIgnore
     * @see Iterator::current()
     */
    public function current() {
        return $this->innerConf->current();
    }
    
    /* 
     * @codeCoverageIgnore
     * @see Iterator::key()
     */
    public function key() {
        return $this->innerConf->key();
    }
    
    /* 
     * @codeCoverageIgnore
     * @see Iterator::next()
     */
    public function next() {
        return $this->innerConf->next();
    }
    
    /* 
     * @codeCoverageIgnore
     * @see Iterator::rewind()
     */
    public function rewind() {
        return $this->innerConf->rewind();
    }
    
    /* 
     * @codeCoverageIgnore
     * @see Iterator::valid()
     */
    public function valid() {
        return $this->innerConf->valid();
    }
    
    /* 
     * @codeCoverageIgnore
     * @see Countable::count()
     */
    public function count() {
        return $this->innerConf->count();
    }
}