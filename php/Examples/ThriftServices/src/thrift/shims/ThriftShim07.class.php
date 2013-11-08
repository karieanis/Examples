<?php
namespace Examples\ThriftServices\Thrift\Shims;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class ThriftShim07 extends ThriftShim {
    const VERSION = "0.7"; // thrift library version
    
    const NAMESPACE_TRANSPORT = "\\";
    const NAMESPACE_PROTOCOL  = "\\";
    
    protected static $paths = array();
    
    /**
     * 
     * @see \Examples\ThriftServices\Thrift\Shims\ThriftShim::getTransport()
     */
    public function getTransport($class, \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf $conf) {
        $args = array();
        
        switch($class) {
            case "TSocket":
            case "TSocketPool":
                $args = $args + array($conf['host'], $conf['port']);
            break;
            
        }
        
        if(($filePath = static::$paths['transport'] . "/" . $class . ".php") &&
            !file_exists($filePath)) {
            throw new ThriftShimException(
                sprintf(
                    "%s in not a valid thrift %s transport class",
                    $class, static::VERSION
                )        
            );
        } else {
            require_once $filePath;            
        }

        try {
            $reflector = new \ReflectionClass(self::NAMESPACE_TRANSPORT . $class);
            
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
        } catch(\ReflectionException $e) {
            // @codeCoverageIgnoreStart
            throw $e;
            // @codeCoverageIgnoreEnd
        }
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Thrift\Shims\ThriftShim::getProtocol()
     */
    public function getProtocol($class, $transport) {
        // certain protocols are not being used in 0.7
        switch($class) {
            case "TBinaryProtocolAccelerated":
            case "TCompactProtocol":
                $class = "TBinaryProtocol";
            break;
        }
        
        if(($filePath = static::$paths['protocol'] . "/" . $class . ".php") &&
                !file_exists($filePath)) {
            throw new ThriftShimException(
                sprintf(
                    "%s in not a valid thrift %s protocol class",
                    $class, static::VERSION
                )
            );
        } else {
            require_once $filePath;
        }
                
        try {
            $reflector = new \ReflectionClass(self::NAMESPACE_PROTOCOL . $class);
            $protocol = $reflector->newInstance($transport);
            
            $this->getLogger()->debug(sprintf(
                    "Created %s protocol instance",
                    get_class($protocol)
                )
            );
            
            return $protocol;
        } catch(\ReflectionException $e) {
            // @codeCoverageIgnoreStart
            throw $e;
            // @codeCoverageIgnoreEnd
        }
    }
    
    /**
     * @see \Examples\ThriftServices\Thrift\Shims\ThriftShim::getPackagesPath()
     * @codeCoverageIgnore
     */
    public function getPackagesPath() {
        return static::$paths['packages'];
    }
    
    /**
     * @see \Examples\ThriftServices\Thrift\Shims\ThriftShim::getRootPath()
     * @codeCoverageIgnore
     */
    public function getRootPath() {
        return static::$paths['root'];
    }
    
    /**
     * 
     * @see \Examples\ThriftServices\Thrift\Shims\ThriftShim::initialize()
     */
    public function initialize() {
        // this is required for the auto generated thrift API
        $GLOBALS['THRIFT_ROOT'] = LIB_ROOT . "/vendor/thrift/" . static::VERSION;

        static::$paths = array(
            "root" => $GLOBALS['THRIFT_ROOT'],
            "packages" => $GLOBALS['THRIFT_ROOT'] . "/packages",
            "transport" => $GLOBALS['THRIFT_ROOT'] . '/transport',
            "protocol" => $GLOBALS['THRIFT_ROOT'] . '/protocol'
        );
        
        require_once static::$paths['root'] . "/Thrift.php";
    }
}