<?php
namespace Examples\Thrift\Shims;

/**
 * Base Thrift shim. This is used to ensure compatibility between different version of thrift with client code
 * which utilises Thrift. Only one shim can be registered at any specific time.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class ThriftShim {
    /**
     * @var boolean
     */
    private static $isRegistered;
    /**
     * @var boolean
     */
    private static $shim;
    
    /**
     * 
     * @var \Logger
     */
    private $logger;
    
    /**
     * Protected constructor, prevents instantiation from outside of this class
     */
    protected final function __construct() {
        $this->setLogger(\Logger::getLogger("ThriftDatabaseLogger"));
    }
    
    abstract public function getPackagesPath();
    abstract public function getRootPath();
    
    /**
     * Generate a transport object using this shim
     * @abstract
     * @param string $class
     * @param \Examples\Hadoop\Conf\HadoopDatabaseConf $conf
     * @return mixed
     */
    abstract public function getTransport($class, \Examples\Hadoop\Conf\HadoopDatabaseConf $conf);
    /**
     * Generate a protocol object using this shim
     * @param string $class
     * @param mixed $transport
     * return mixed
     */
    abstract public function getProtocol($class, $transport);
    /**
     * Initialize this shim (set up loaders, dependencies, etc)
     */
    abstract public function initialize();
    
    protected final function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    protected final function getLogger() {
        return $this->logger;
    }
    
    /**
     * Register the current shim
     * @static
     * @throws ThriftShimException
     */
    public final static function register() {
        if(!self::$isRegistered) {
            $impl = new static();
            $impl->initialize();
            $impl->getLogger()->info(sprintf("Registering thrift shim class %s", get_class($impl)));
            
            self::$isRegistered = true;
            self::$shim = $impl;
        } else {
            throw new ThriftShimException(
                sprintf(
                    "%s is currently registered as the running thrift shim and cannot be changed", 
                    get_class(static::getShim())
                )
            );
        }
    }
    
    /**
     * Get the registered shim
     * @return \Examples\Thrift\Shims\ThriftShim
     */
    public final static function getShim() {
        return self::$shim;
    }
}