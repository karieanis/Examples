<?php
namespace Examples\ThriftServices\Hive\Service;

/**
 * Bootstrapping class for Hive Server 2 interaction
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HiveServer2 extends \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService {
    protected static $ServiceClass     = "\TCLIServiceClient";
    protected static $ConnectionClass  = "\Examples\ThriftServices\Hive\HiveDb";
    
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
        $transport = \Examples\ThriftServices\Thrift\Shims\ThriftShim::getShim()->getTransport($class, $conf);
        
        // wrap in SASL transport if required
        return \Examples\ThriftServices\Thrift\Transport\TSaslTransportFactory::factory(
            $transport, $conf['auth_mechanism'], $conf['username'], $conf['password']
        );
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
        \Examples\ThriftServices\Thrift\Shims\ThriftShim09::register();
        $shim = \Examples\ThriftServices\Thrift\Shims\ThriftShim::getShim();
        
        // service files
        require_once $shim->getPackagesPath() . "/cli_service/TCLIService.php";
        require_once $shim->getPackagesPath() . "/cli_service/Types.php";
    }
    
    protected function registerConf() {
        // register the conf object with the conf factory
        \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::register("\Examples\ThriftServices\Hive\Conf\HiveConf");
    }
}