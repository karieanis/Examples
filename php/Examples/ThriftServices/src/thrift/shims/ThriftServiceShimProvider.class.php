<?php
namespace Examples\ThriftServices\Thrift\Shims;

/**
 * Provides shims for services
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class ThriftServiceShimProvider {
    /**
     * 
     * @param \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService $service
     * @throws Exception
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShim
     */
    public static function getShim(\Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService $service) {
        $logger = static::getLogger();
        
        $ShimClass = $service->getShimClass();
        $version = $ShimClass::getVersion();
        
        try {
            $logger->debug(sprintf("Attempting to get Thrift Shim for %s", get_class($service)));
            $shim = \Examples\ThriftServices\Thrift\Shims\ThriftShimProvider::getShim($version);
        } catch(\Examples\ThriftServices\Factory\NotRegisteredException $e) {
            $logger->debug(sprintf("No shim registered for %s, will register and try again", get_class($service)), $e);
            \Examples\ThriftServices\Thrift\Shims\ThriftShimFactory::getInstance()->register(
                $version, $ShimClass
            );
        
            $shim = \Examples\ThriftServices\Thrift\Shims\ThriftShimProvider::getShim($version);
        } catch(\Exception $e) {
            $logger->debug(
                sprintf("An unexpected exception occured whilst attempting to retrieve a shim for %s", get_class($service)), 
                $e
            );
            
            throw $e;
        }
        
        return $shim;
    }
    
    /**
     * @return \Logger
     */
    protected static function getLogger() {
        return \Logger::getLogger('servicesLogger');
    } 
}