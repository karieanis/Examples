<?php
namespace Examples\Thrift\Shims;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class ThriftShim09 extends ThriftShim {
    const VERSION = "0.9"; // thrift library version
    
    const NAMESPACE_TRANSPORT = "\\Thrift\\Transport\\";
    const NAMESPACE_PROTOCOL  = "\\Thrift\\Protocol\\";
    
    protected static $paths = array();
    
    /**
     * 
     * @see \Examples\Thrift\Shims\ThriftShim::getTransport()
     */
    public function getTransport($class, \Examples\Hadoop\Conf\HadoopDatabaseConf $conf) {
        $args = array();
        
        switch($class) {
            case "TSocket":
            case "TSocketPool":
                $args = $args + array($conf['host'], $conf['port']);
            break;
        }
        
        try {
            $reflector = new \ReflectionClass(self::NAMESPACE_TRANSPORT . $class);
            
            /* @var $transport \Thrift\Transport\TTransport */
            $transport = $reflector->newInstanceArgs($args);
            $transport->setRecvTimeout($conf['receive_timeout']);
            $transport->setSendTimeout($conf['send_timeout']);
            $transport->setDebug($conf['debug']);
            
            $this->getLogger()->debug(sprintf(
                    "Created %s transport instance",
                    get_class($transport)
                )
            );
            
            return $transport;
        } catch(\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 
     * @see \Examples\Thrift\Shims\ThriftShim::getProtocol()
     */
    public function getProtocol($class, $transport) {
        try {
            $reflector = new \ReflectionClass(self::NAMESPACE_PROTOCOL . $class);
            /* @var $protocol \Thrift\Protocol\TProtocol */
            $protocol = $reflector->newInstance($transport);
            
            $this->getLogger()->debug(sprintf(
                    "Created %s protocol instance",
                    get_class($protocol)
                )
            );
            
            return $protocol;
        } catch(\ReflectionException $e) {
            throw $e;
        }
    }
    
    /**
     * 
     * @see \Examples\Thrift\Shims\ThriftShim::getPackagesPath()
     */
    public function getPackagesPath() {
        return static::$paths['packages'];
    }
    
    /**
     * @see \Examples\Thrift\Shims\ThriftShim::getRootPath()
     * @codeCoverageIgnore
     */
    public function getRootPath() {
        return static::$paths['root'];
    }
    
    /**
     * 
     * @see \Examples\Thrift\Shims\ThriftShim::initialize()
     */
    public function initialize() {
        $thriftRoot = LIB_ROOT . "/vendor/thrift/" . static::VERSION;
        $thriftPackageRoot = $thriftRoot . "/packages";
        $thriftLibraryRoot = $thriftRoot . "/lib";
        
        static::$paths = array(
            "root" => $thriftRoot,
            "packages" => $thriftPackageRoot
        );
        
        // thrift autoloader
        require_once $thriftLibraryRoot . "/Thrift/ClassLoader/ThriftClassLoader.php";
        $thriftLoader = new \Thrift\ClassLoader\ThriftClassLoader();
        $thriftLoader->registerNamespace("Thrift", $thriftLibraryRoot);
        $thriftLoader->register();
    }
}