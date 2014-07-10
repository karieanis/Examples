<?php
namespace Examples\ThriftServices\Hive;

/**
 * Hive communication layer, utilises a PDO like interface
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @codeCoverageIgnore
 */
class HivePDO {
    const FETCH_ASSOC = 1;
    const FETCH_NUM = 2;
    const FETCH_BOTH = 3;
    
    const FETCH_ORI_NEXT = 1;
    
    /**
     * 
     * @var \Examples\ThriftServices\Thrift\Service\Container
     */
    protected $ServiceContainer;
    
    /**
     * 
     * @var string
     */
    protected $username;
    /**
     * 
     * @var string
     */
    protected $database;
    /**
     * 
     * @var string
     */
    protected $password;
    
    /**
     * 
     * @var \Logger
     */
    protected $logger;
    
    /**
     * Constructor
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public function __construct($dsn, $username = 'hive', $password = '') {
        $this->logger = \Logger::getLogger('servicesLogger');
        $conf = $this->getHiveConf(
            array_merge(
                $this->parseDsn($dsn),
                array(
                    'username' => $username,
                    'password' => $password
                )
            )        
        );

        $this->setServiceContainer(\Examples\ThriftServices\Thrift\Service\Factory::factory($conf))
            ->setDatabase($conf['dbname'])
            ->setUsername($username)
            ->setPassword($password);
    }
    
    /**
     * 
     * @param array $overrides
     * @return \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     */
    protected function getHiveConf(array $overrides) {
        $conf = \Examples\ThriftServices\Hadoop\Service\HadoopDatabaseService::getRunningService()->getConf();
        
        foreach($overrides as $key => $value) {
            $conf[$key] = $value;
        }
        
        return $conf;
    }
    
    /**
     * Execute the passed statement and return the number of affected rows
     * @param string $statement
     * @return int
     */
    public function exec($statement) {
        $this->useDatabase();
        $class = $this->getStatementClass();
        
        /* @var \Examples\ThriftServices\Hive\HivePDOStatement $handle */
        $handle = new $class($this);
        $handle->queryString = $statement;
        $handle->execute();
        
        $count = $handle->rowCount();
        $handle->close();
        
        return $count;
    }
    
    /**
     * Prepare a HivePDOStatement object with the passed statement, then return the object
     * @param string $statement
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     */
    public function prepare($statement) {
        $this->useDatabase();
        $class = $this->getStatementClass();
        
        /* @var \Examples\ThriftServices\Hive\HivePDOStatement $handle */
        $handle = new $class($this);
        $handle->queryString = $statement;
        
        return $handle;
    }
    
    /**
     * Execute the passed statement, then return a HivePDOStatement object
     * @param string $statement
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     */
    public function query($statement) {
        $this->useDatabase();
        $class = $this->getStatementClass();
        
        /* @var \Examples\ThriftServices\Hive\HivePDOStatement $handle */
        $handle = new $class($this);
        $handle->queryString = $statement;
        $handle->execute();
        
        return $handle;
    }
       
    /**
     * Return a string representing the statement class to instantiate
     * @return string
     */
    protected function getStatementClass() {
        return "\Examples\ThriftServices\Hive\HivePDOStatement";
    }
    
    /**
     * Ensure that the correct database has been selected for this query session
     * @return void
     */
    protected function useDatabase() {
        $class = $this->getStatementClass();
        
        $handle = new $class($this);
        $handle->queryString = sprintf("USE %s", (string)$this->getDatabase());
        $handle->execute();
        $handle->close();
    }
    
    /**
     * Get the current thrift service container object
     * @return \Examples\ThriftServices\Thrift\Service\Container
     */
    protected function getServiceContainer() {
        return $this->ServiceContainer;
    }
    
    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
    
    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
    
    /**
     * Get the name of the currently connected database
     * @return string
     */
    public function getDatabase() {
        return $this->database;
    }
    
    /**
     * @return \Thrift\Transport\TTransport
     */
    public function getTransport() {
        return $this->getServiceContainer()->getTransport();
    }
    
    /**
     * @return \Thrift\Protocol\TProtocol
     */
    protected function getProtocol() {
        return $this->getServiceContainer()->getProtocol();
    }
   
    /**
     * @return \apache\hive\service\cli\thrift\TCLIServiceIf
     */
    public function getClient() {
        return $this->getServiceContainer()->getClient();
    }
    
    /**
     * Parse the DSN string into an array of key value pairs
     * @param string $dsn
     * @return array
     */
    protected function parseDsn($dsn) {
        $helper = new \Examples\ThriftServices\Database\Helpers\DSNHelper();
        return $helper->parse($dsn);
    }
    
    /**
     * Set the thrift service container
     * @param \Examples\ThriftServices\Thrift\Service\Container $ServiceContainer
     * @return \Examples\ThriftServices\Hive\HivePDO
     */
    protected function setServiceContainer(\Examples\ThriftServices\Thrift\Service\Container $ServiceContainer) {
        $this->ServiceContainer = $ServiceContainer;
        return $this;
    }
    
    /**
     * @param string $username
     * @return \Examples\ThriftServices\Hive\HivePDO
     */
    protected function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    /**
     * 
     * @param string $password
     * @return \Examples\ThriftServices\Hive\HivePDO
     */
    protected function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    /**
     * @param string $database
     * @return \Examples\ThriftServices\Hive\HivePDO
     */
    protected function setDatabase($database) {
        $this->database = $database;
        return $this;
    }
    
    /**
     * Clean up any open sessions and close the current transport connection
     */
    public function __destruct() {
        $this->logger->debug("Destroying PDO, clearing existing sessions");
        $manager = Session\HiveSessionCollection::getInstance();

        /* @var Session\HiveSession $session */
        foreach($manager->get() as $session) {
            $session->close($this->getClient());
        }
        
        $manager->clear(); // clear out
        $this->logger->debug(sprintf("%d session(s) left in session manager", count($manager->get())));
        $this->getTransport()->close();
    }
}