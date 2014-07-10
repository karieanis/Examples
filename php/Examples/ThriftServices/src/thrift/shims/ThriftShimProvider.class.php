<?php
namespace Examples\ThriftServices\Thrift\Shims;

/**
 * Handles shim provisioning and instantiation
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ThriftShimProvider {
    /**
     * @static
     * @param mixed $key
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShim
     */
    public static function getShim($key) {
        $logger = static::getLogger();
        $Pool = \Examples\ThriftServices\Thrift\Shims\ThriftShimPool::getInstance();
        
        if(!$Pool->has($key)) {
            $instance = \Examples\ThriftServices\Thrift\Shims\ThriftShimFactory::getInstance()->create($key);
            
            $logger->debug(sprintf("Shim v%s not found in %s, adding", $key, get_class($Pool)));
            $Pool->set($key, $instance);
        }
        
        $logger->debug(sprintf("Shim v%s available in %s, returning", $key, get_class($Pool)));
        return $Pool->get($key);
    }
    
    /**
     * @return \Logger
     */
    protected static function getLogger() {
        return \Logger::getLogger('servicesLogger');
    }
}