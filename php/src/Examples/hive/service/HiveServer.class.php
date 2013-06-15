<?php
namespace Examples\Hive\Service;

/**
 * Bootstrapping class for Hive Server interaction.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HiveServer extends \Examples\Hadoop\Service\HadoopDatabaseService {
    protected static $ServiceClass     = "\ThriftHiveClient";
    protected static $ConnectionClass  = "\HiveServerDb";
    
    /**
     * 
     * @see \Examples\Hadoop\Service\HadoopDatabaseService::getConnection()
     */
    public function getConnection($host, $port, $database) {
        return \Examples\Hive\HiveConnectionPool::getInstance()->getConnection(
            static::getConnectionClass(), $host, $port, $database
        );
    }
    
    /**
     * 
     * @see \Examples\Hadoop\Service\HadoopDatabaseService::getConnectionClass()
     */
    public function getConnectionClass() {
        return static::$ConnectionClass;
    }
    
    /**
     * 
     * @see \Examples\Hadoop\Service\HadoopDatabaseService::getServiceClass()
     */
    public function getServiceClass() {
        return static::$ServiceClass;
    }
    
    /**
     * 
     * @see \Examples\Hadoop\Service\HadoopDatabaseService::getTransport()
     */
    public function getTransport($class, \Examples\Hadoop\Conf\HadoopDatabaseConf $conf) {
        return \Examples\Thrift\Shims\ThriftShim::getShim()->getTransport($class, $conf);
    }
    
    /**
     * 
     * @see \Examples\Hadoop\Service\HadoopDatabaseService::getProtocol()
     */
    public function getProtocol($class, $transport) {
        return \Examples\Thrift\Shims\ThriftShim::getShim()->getProtocol($class, $transport);
    }
        
    /**
     * 
     * @see \Examples\Hadoop\Service\HadoopDatabaseService::initialize()
     */
    protected function initialize() {
        \Examples\Thrift\Shims\ThriftShim07::register();
        $shim = \Examples\Thrift\Shims\ThriftShim::getShim();
        
        require_once $shim->getPackagesPath() . '/hive_service/ThriftHive.php';
    }
    
    protected function registerConf() {
        // register the conf object with the conf factory
        \Examples\Hadoop\Conf\HadoopDatabaseConfFactory::register("\Examples\Hive\Conf\HiveConf");
    }
}