<?php
namespace Examples\ThriftServices\Thrift\Service;

final class ThriftServiceProvider {
    public static function getService($key) {
        $logger = static::getLogger();
        $Pool = \Examples\ThriftServices\Thrift\Service\ThriftServicePool::getInstance();
        
        if(!$Pool->has($key)) {
            $instance = \Examples\ThriftServices\Thrift\Service\ThriftServiceFactory::getInstance()->create($key);
            
            $logger->debug(sprintf("Service %s not found in %s, adding", $key, get_class($Pool)));
            $Pool->set($key, $instance);
        }
        
        $logger->debug(sprintf("Service %s available in %s, returning", $key, get_class($Pool)));
        return $Pool->get($key);
    }
    
    /**
     * @return \Logger
     */
    protected static function getLogger() {
        return \Logger::getLogger('servicesLogger');
    }
}