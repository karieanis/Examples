<?php
namespace Examples\ThriftServices\Impala\Service;

/**
 * Impala service implementation. Utilises HiveServer2 Client IDL for RPC.
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class ImpalaService 
    extends \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService 
    implements \Examples\ThriftServices\Thrift\Service\IShimmedService,
               \Examples\ThriftServices\Conf\IConfigurable {
    
    protected static $ServiceClass     = "\\apache\\hive\\service\\cli\\thrift\\TCLIServiceClient";
    protected static $ConnectionClass  = "\Examples\ThriftServices\Hive\HiveDb";
    protected static $ConfClass        = "\Examples\ThriftServices\Impala\Conf\ImpalaConf";
    protected static $ShimClass        = "\Examples\ThriftServices\Thrift\Shims\ThriftShim09";
    
    /**
     * 
     * @var \Examples\ThriftServices\Thrift\Shims\ThriftShim
     */
    protected $Shim;
    /**
     * 
     * @var \Examples\ThriftServices\Conf\BaseConf
     */
    protected $Conf;
    
    /**
     * @see \Examples\ThriftServices\Thrift\Service\IShimmedService::getShim()
     */
    public function getShim() {
        return $this->Shim;
    }
    
    /**
     * @see \Examples\ThriftServices\Thrift\Service\IShimmedService::setShim()
     */
    public function setShim(\Examples\ThriftServices\Thrift\Shims\ThriftShim $shim) {
        $this->Shim = $shim;
        return $this;
    }
    
    /**
     * 
     * @return \Examples\ThriftServices\Conf\BaseConf
     */
    public function getConf() {
        return $this->Conf;
    }
    
    /**
     * 
     * @param \Examples\ThriftServices\Conf\BaseConf $conf
     * @return \Examples\ThriftServices\Hive\Service\HiveServer
     */
    public function setConf(\Examples\ThriftServices\Conf\BaseConf $conf) {
        $this->Conf = $conf;
        return $this;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getConnection()
     */
    public function getConnection($database) {
        $ServiceConf = $this->getConf();
        return \Examples\ThriftServices\Hive\HiveConnectionPool::getInstance()->getConnection(
            $this->getConnectionClass(), $ServiceConf['host'], $ServiceConf['port'], $database
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
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getConfClass()
     */
    public function getConfClass() {
        return static::$ConfClass;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getShimClass()
     */
    public function getShimClass() {
        return static::$ShimClass;
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getTransport()
     */
    public function getTransport($class, \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        return $this->getShim()->getTransport($class, $conf);
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getProtocol()
     */
    public function getProtocol($class, $transport) {
        return $this->getShim()->getProtocol($class, $transport);
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::initialize()
     */
    public function initialize() {
        $shim = \Examples\ThriftServices\Thrift\Shims\ThriftServiceShimProvider::getShim($this);
        $conf = \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::create($this->getConfClass());
        
        // service files
        require_once $shim->getPackagesPath() . "/apache/hive/service/cli/thrift/TCLIService.php";
        require_once $shim->getPackagesPath() . "/apache/hive/service/cli/thrift/Types.php";
        
        $this->setShim($shim)
            ->setConf($conf);
    }
    
    /**
     * Clear out existing connections in the connection pool when this service is destroyed
     */
    public function __destruct() {
        $this->getLogger()->debug(sprintf("Destroying % service instance, clearing out existing connections", get_class($this)));
        \Examples\ThriftServices\Hive\HiveConnectionPool::getInstance()->clear();
    }
}