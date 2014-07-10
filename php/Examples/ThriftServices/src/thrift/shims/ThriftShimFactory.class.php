<?php
namespace Examples\ThriftServices\Thrift\Shims;

/**
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ThriftShimFactory extends \Examples\ThriftServices\Factory\BaseFactory {
    /**
     * @staticvar \Examples\ThriftServices\Thrift\Shims\ThriftShimFactory
     */
    protected static $instance;
    /**
     * @staticvar array
     */
    protected static $ShimMap = array();
    
    /**
     * Protected constructor, prevents direct instantiation
     */
    protected function __construct() {
        $this->setLogger(\Logger::getLogger('servicesLogger'));
    }
    
    /**
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShimFactory
     */
    public static function getInstance() {
        if(!(self::$instance instanceof static)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }

    /**
     * @return array
     */
    public function &getClassMap() {
        return static::$ShimMap;
    }

    /**
     * @see \Examples\ThriftServices\Factory\BaseFactory::postCreate()
     */
    protected function postCreate($instance) {
        /* @var $instance \Examples\ThriftServices\Thrift\Shims\ThriftShim */
        $instance->initialize();
    }
}