<?php
namespace Examples\ThriftServices\Thrift\Service;

final class ThriftServicePool extends \Examples\ThriftServices\Pool\BasePool {
    /**
     * @staticvar \Examples\ThriftServices\Thrift\Service\ThriftServicePool
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
     * @return \Examples\ThriftServices\Thrift\Service\ThriftServicePool
     */
    public static function getInstance() {
        if(!(static::$instance instanceof static)) {
            static::$instance = new static;
        }
    
        return static::$instance;
    }
}