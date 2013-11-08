<?php
namespace Examples\ThriftServices\Hadoop\Service;

use Examples\ThriftServices\Hadoop\Conf\HadoopConfFactory;

use Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory;

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
    private static $registeredService;

    /**
     * 
     * @var \Logger
     */
    private $logger;
    
    /**
     * Protected constructor, ensures all subclasses are basically static classes
     */
    protected final function __construct() { 
        $this->setLogger(\Logger::getLogger("ThriftDatabaseLogger"));
    }

    /**
     * Get the connection class name
     * @return string
     */
    abstract public function getConnectionClass();
    /**
     * Get the current service class
     * @return string
     */
    abstract public function getServiceClass();
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
     * @param string $host
     * @param int $port
     * @param string $database
     * @return \IHiveDb
     */
    abstract public function getConnection($host, $port, $database);
    /**
     * Initialization method. Implementation classes need to set up all their dependencies when this method is fired, etc.
     * Set up autloaders, include service files.
     * @return void
     */
    abstract protected function initialize();
    
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
     * Bootstrapping service for differing implementations of hadoop database services
     * @throws HadoopDatabaseServiceException
     */
    public final static function register() {
        if(!self::$isRegistered) {
            $impl = new static();
            
            $impl->initialize();
            $impl->getLogger()->info(sprintf("Registering service class %s", get_class($impl)));
            $impl->registerConf();
            
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
     * Get the current running Hive Server service
     * @return \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService
     */
    public final static function getRunningService() {
        return self::$registeredService;
    }
    
    abstract protected function registerConf();
}