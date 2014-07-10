<?php
namespace Examples\ThriftServices\Pool;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class BasePool implements IPool {
    /**
     * @return array
     */
    abstract public function &getPoolMap();
    
    /**
     * 
     * @see \Examples\ThriftServices\Pool\IPool::has()
     */
    final public function has($key) {
        $map = &$this->getPoolMap();
        return isset($map[$key]);
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Pool\IPool::set()
     */
    final public function set($key, $item) {
        $map = &$this->getPoolMap();
        $map[$key] = $item;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Pool\IPool::get()
     */
    final public function get($key) {
        $map = &$this->getPoolMap();
        return $map[$key];
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Pool\IPool::remove()
     */
    final public function remove($key) {
        $map = &$this->getPoolMap();
        unset($map[$key]);
    }
    
    /**
     * Clear out everything in the pool
     */
    final public function clear() {
        $map = &$this->getPoolMap();

        foreach(array_keys($map) as $key) {
            $this->remove($key);
        }
    }
}