<?php
namespace Examples\ThriftServices\Thrift\Shims;

/**
 * Base Thrift shim. This is used to ensure compatibility between different version of thrift with our client code
 * which utilises Thrift. Multiple shims can be registered at any specific time.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
abstract class ThriftShim {
    /**
     * 
     * @var \Logger
     */
    private $logger;
    
    /**
     * Protected constructor, prevents instantiation from outside of this class
     */
    public final function __construct() {
        $this->setLogger(\Logger::getLogger("servicesLogger"));
    }
    
    /**
     * @return string
     */
    abstract public function getPackagesPath();
    /**
     * @return string
     */
    abstract public function getRootPath();
    /**
     * Generate a transport object using this shim
     * @abstract
     * @param string $class
     * @param \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf
     * @return mixed
     */
    abstract public function getTransport($class, \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf);
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
    
    /**
     * @final
     * @param \Logger $logger
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShim
     */
    protected final function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * @final
     * @return \Logger
     */
    protected final function getLogger() {
        return $this->logger;
    }
    
    /**
     * @final
     * @static
     * @return string
     */
    public final static function getVersion() {
        return static::VERSION;
    }
}