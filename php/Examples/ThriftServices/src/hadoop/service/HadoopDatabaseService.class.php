<?php
namespace Examples\ThriftServices\Hadoop\Service;

use Examples\ThriftServices\Hadoop\Conf\HadoopConfFactory,
    Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory;

/**
 * Base class for hadoop database service implementations. Also responsible for the generation of an instance and ensuring that only
 * one service can be registered at any given time.
 * 
 * @abstract
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
abstract class HadoopDatabaseService {
    /**
     * Internal flag used to indicate when a service has been registered
     * @var boolean
     */
    private static $isRegistered;
    /**
     * The current running implementation
     * @var \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService
     */
    private static $registeredService = null;
    /**
     * The current service configuration
     * @var \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     */
    private static $serviceConf;
    
    /**
     * 
     * @var \Logger
     */
    private $logger;
    
    /**
     * Protected constructor, ensures all subclasses are basically static classes
     */
    public final function __construct() { 
        $this->setLogger(\Logger::getLogger("servicesLogger"));
    }

    /**
     * Get the connection class name
     * @return string
     */
    abstract public function getConnectionClass();
    /**
     * Get the service class name for this instance
     * @return string
     */
    abstract public function getServiceClass();
    /**
     * Get the conf class name for this instance
     * @return string
     */
    abstract public function getConfClass();
    /**
     * Get the shim class name for this instance
     * @return string
     */
    abstract public function getShimClass();
    /**
     * Get a transport object for this service
     * @param string $class
     * @param \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf
     */
    abstract public function getTransport($class, \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf);
    /**
     * Get a protocol object for this service
     * @param string $class
     * @param mixed $transport
     */
    abstract public function getProtocol($class, $transport);
    /**
     * Return a IHiveDb client class relevant to the concrete service
     * 
     * @param string $database
     * @return \IHiveDb
     */
    abstract public function getConnection($database);
    /**
     * Initialization method. Implementation classes need to set up all their dependencies when this method is fired, etc.
     * Set up autloaders, include service files.
     * @return void
     */
    abstract public function initialize();
    
    /**
     * 
     * @param \Logger $logger
     * @return \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService
     */
    protected final function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * 
     * @return \Logger
     */
    protected final function getLogger() {
        return $this->logger;
    }

    /**
     * Get the service conf object
     * @return \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     */
    protected final function getServiceConf() {
        return $this->getConf();
    }
    
    /**
     * Bootstrapping service for differing implementations of hadoop database services
     * @throws HadoopDatabaseServiceException
     */
    public final static function register() {
        if(!self::$isRegistered) {
            $ChildClass = "\\" . get_called_class();
            $ObjectKey = AvailableServices::getInstance()->getKeyByServiceClass($ChildClass);
            
            try {
                $impl = \Examples\ThriftServices\Thrift\Service\ThriftServiceProvider::getService($ObjectKey);
            } catch(\Examples\ThriftServices\Factory\NotRegisteredException $e) {
                \Examples\ThriftServices\Thrift\Service\ThriftServiceFactory::getInstance()->register($ObjectKey, $ChildClass);
                $impl = \Examples\ThriftServices\Thrift\Service\ThriftServiceProvider::getService($ObjectKey);
            }
            
            $impl->getLogger()->info(sprintf("Registering service class %s", get_class($impl)));
            
            self::$isRegistered = true;
            self::$registeredService = $impl;
        } else {
            throw new HadoopDatabaseServiceException(
                sprintf(
                    "%s is currently registered as the running hive service and cannot be changed",
                    get_class(static::getRunningService())
                )
            );
        }
    }

    /**
     * Deregisters the currently registered service
     */
    public final static function deregister() {
        if(self::$isRegistered) {
            $impl = self::getRunningService();
            $impl->getLogger()->debug(sprintf("Deregistering %s service instance, clearing out existing connections", get_class($impl)));
            \Examples\ThriftServices\Hive\HiveConnectionPool::getInstance()->clear();
            unset($impl);
            
            self::$isRegistered = false;
            self::$registeredService = null;
        }
    }
    
    /**
     * Get the current running Hive Server service
     * @return \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService
     */
    public final static function getRunningService() {
        return self::$registeredService;
    }
}