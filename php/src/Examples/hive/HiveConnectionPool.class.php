<?php
namespace Examples\Hive;

/**
 * Simple connection pool singleton class
 * @author Jeremy Rayner <jeremy@davros.com.au>
 */
class HiveConnectionPool {
    /**
     * 
     * @var HiveConnectionPool
     */
    protected static $instance;
    
    /**
     * 
     * @var \Logger
     */
    protected $logger;
    /**
     * 
     * @var array
     */
    protected $pool = array();

    /**
     * Constructor
     */
    protected function __construct() {
        $this->setLogger(\Logger::getLogger("ThriftDatabaseLogger"));
    }
    
    /**
     * @return \Examples\Hive\HiveConnectionPool
     */
    public static function getInstance() {
        if(!(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        
        return static::$instance;
    }
    
    /**
     * 
     * @return Logger
     */
    public function getLogger() {
        return $this->logger;
    }
    
    /**
     * 
     * @param \Logger $logger
     * @return \Examples\Hive\HiveConnectionPool
     */
    public function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Attempt to retrieve a connection from the pool. If a connection does not exist, instantiate it, add it to the pool,
     * then return the connection.
     * 
     * @param string $class
     * @param string $host
     * @param int $port
     * @param string $database
     * 
     * @return \IHiveDb
     */
    public function getConnection($class, $host, $port, $database) {
        $hashKey = md5(implode(".", array($class, $host, $port, $database)));
        
        // connection is cached in the pool
        if(isset($this->pool[$hashKey])) {
            $this->getLogger()->debug(
                sprintf(
                    "Connection %s[host=%s, port=%s, database=%s] retrieved from pool",
                    $class, $host, $port, $database
                )
            );
            
            $connection = $this->pool[$hashKey];
        } else { // no connection found, instantiate and add to the pool
            $this->getLogger()->debug(
                sprintf(
                    "Connection %s[host=%s, port=%s, database=%s] not found in pool, adding",
                    $class, $host, $port, $database
                )        
            );
            
            $this->pool[$hashKey] = $connection = new $class($host, $port, $database);
        }
        
        return $connection;
    }
}