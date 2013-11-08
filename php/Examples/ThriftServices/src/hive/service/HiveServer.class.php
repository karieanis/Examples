<?php
namespace Examples\ThriftServices\Hive\Service;

/**
 * Bootstrapping class for Hive Server interaction.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HiveServer extends \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService {
    protected static $ServiceClass     = "\ThriftHiveClient";
    protected static $ConnectionClass  = "\HiveServerDb";
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getConnection()
     */
    public function getConnection($host, $port, $database) {
        return \Examples\ThriftServices\Hive\HiveConnectionPool::getInstance()->getConnection(
            static::getConnectionClass(), $host, $port, $database
        );
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getConnectionClass()
     */
    public function getConnectionClass() {
        return static::$ConnectionClass;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getServiceClass()
     */
    public function getServiceClass() {
        return static::$ServiceClass;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getTransport()
     */
    public function getTransport($class, \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        return \Examples\ThriftServices\Thrift\Shims\ThriftShim::getShim()->getTransport($class, $conf);
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getProtocol()
     */
    public function getProtocol($class, $transport) {
        return \Examples\ThriftServices\Thrift\Shims\ThriftShim::getShim()->getProtocol($class, $transport);
    }
        
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::initialize()
     */
    protected function initialize() {
        \Examples\ThriftServices\Thrift\Shims\ThriftShim07::register();
        $shim = \Examples\ThriftServices\Thrift\Shims\ThriftShim::getShim();
        
        require_once $shim->getPackagesPath() . '/hive_service/ThriftHive.php';
    }
    
    protected function registerConf() {
        // register the conf object with the conf factory
        \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::register("\Examples\ThriftServices\Hive\Conf\HiveConf");
    }
}