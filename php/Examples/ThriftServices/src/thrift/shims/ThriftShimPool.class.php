<?php
namespace Examples\ThriftServices\Thrift\Shims;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ThriftShimPool extends \Examples\ThriftServices\Pool\BasePool {
    /**
     * @staticvar \Examples\ThriftServices\Thrift\Shims\ThriftShimPool
     */
    protected static $instance;
    /**
     * @staticvar array
     */
    protected static $pool = array();
    
    /**
     * Protected constructor, prevent direct instantiation
     */
    protected function __construct() {
        
    }
    
    /* 
     * @see \Examples\ThriftServices\Pool\BasePool::getPoolMap()
     */
    public function &getPoolMap() {
        return static::$pool;
    }
    
    /**
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShimPool
     */
    public static function getInstance() {
        if(!(static::$instance instanceof static)) {
            static::$instance = new static;
        }
        
        return static::$instance;
    }
}