<?php
namespace Examples\ThriftServices\Thrift\Service;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ThriftServiceFactory extends \Examples\ThriftServices\Factory\BaseFactory {
    /**
     * 
     * @staticvar \Examples\ThriftServices\Thrift\Service\ThriftServiceFactory
     */
    protected static $instance;
    /**
     * 
     * @staticvar array
     */
    protected static $ServiceMap = array();
    
    /**
     * Protected constructor, prevents direct instantiation
     */
    protected function __construct() {
        $this->setLogger(\Logger::getLogger('servicesLogger'));
    }
    
    /**
     * 
     * @return \Examples\ThriftServices\Thrift\Service\ThriftServiceFactory
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
        return static::$ServiceMap;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Factory\BaseFactory::postCreate()
     */
    protected function postCreate($instance) {
        /* @var $instance \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService */
        $instance->initialize();
    }
}