<?php
namespace Examples\ThriftServices\Hive;

/**
 * This class is so we can maintain a consistent interface across all the various DB classes
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class HiveDb implements \IHiveDb {
    /**
     * Underlying PDO object
     * @var \Examples\ThriftServices\Hive\HivePDO
     */
    protected $pdo;
    /**
     * a log4php Logger
     * @var \Logger
     */
    protected $logger;
    
    /**
     * Constructor
     * @param string $host
     * @param int $port
     * @param string $database
     * @throws \Exception
     */
    public function __construct($host, $port = 10000, $database = 'default') {
        try {
            $ServiceConf = $this->getServiceConf();
            $helper = new \Examples\ThriftServices\Database\Helpers\DSNHelper();
                        
            $pdo = new HivePDO($helper->build("hive2", $host, $port, $database),
                isset($ServiceConf['username']) ? $ServiceConf['username'] : "hive",
                isset($ServiceConf['password']) ? $ServiceConf['password'] : "");
        } catch (\Exception $e) {
            throw $e;
        }
        
        $this->setPDO($pdo)
            ->setLogger(\Logger::getLogger('emQueryLogger'));
    }
    
    /**
     * Executes the passed query and returns a HiveDBStatement object
     * @param string $query
     * @throws \Exception
     * @return \Examples\ThriftServices\Hive\HiveDbStatement
     */
    public function execute($query) {
        if(is_null($queries = json_decode($query))) {
            $queries = array($query);
        }
        
        foreach($queries as $query) {
            if(\Director\Lib\Tools\Config::get('app>log_query')) {
                $this->getLogger()->info($query);
            }
            
            try {
                $statement = $this->getPDO()->query($query);
            } catch(\Exception $e) {
                throw $e;
            }
        }
        
        return new HiveDbStatement($statement);
    }
    
    /**
     * Executes the passed query and returns a HiveDBStatement object
     * @param string $query
     * @return \Examples\ThriftServices\Hive\HiveDbStatement
     */
    public function fetchStmt($query) {
        return $this->execute($query);
    }
    
    /**
     * Check if a connection is open to HiveServer2
     * @return boolean
     */
    public function isConnected() {
        return $this->getPDO()->getTransport()->isOpen();
    }
    
    /**
     * Get the current Thrift service client
     * @return \apache\hive\service\cli\thrift\TCLIServiceIf
     */
    public function getClient() {
        return $this->getPDO()->getClient();
    }
    
    /**
     * 
     * @return \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     */
    protected function getServiceConf() {
        return \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getRunningService()->getConf();
    }
    
    /**
     * Get the current logging instance
     * @return \Logger
     */
    protected function getLogger() {
        return $this->logger;
    }
    
    /**
     * Set a log4php compatible Logger
     * @param \Logger $logger
     * @return \Examples\ThriftServices\Hive\HiveDb
     */
    protected function setLogger(\Logger $logger) {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Get the underlying HivePDO object
     * @return \Examples\ThriftServices\Hive\HivePDO
     */
    protected function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Set the underlying HivePDO object
     * @param HivePDO $pdo
     * @return \Examples\ThriftServices\Hive\HiveDb
     */
    protected function setPDO(HivePDO $pdo) {
        $this->pdo = $pdo;
        return $this;
    }
}
