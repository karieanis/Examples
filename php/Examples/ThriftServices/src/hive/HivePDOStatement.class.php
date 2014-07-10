<?php
namespace Examples\ThriftServices\Hive;

/**
 * Hive PDO statement class, utilises a PDOStatement like interface. Outwardly, the use of this class is consistent with
 * that of PDOStatement.
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HivePDOStatement {
    /**
     * The current fetch style
     * @var integer
     */
    protected $fetch_style = null;
    /**
     * HiveServer2 operation handle
     * @var \apache\hive\service\cli\thrift\TOperationHandle
     */
    protected $operationHandle;
    /**
     * PDO object
     * @var HivePDO
     */
    protected $pdo;
    /**
     * The current result set
     * @var HivePDOResultSet
     */
    protected $resultSet;
    
    /**
     * Schema metadata object
     * @var Meta\Schema
     */
    protected $schema;
    
    /**
     * The current fetch size
     * @var integer
     */
    protected $fetchSize = 50;
    
    /**
     * Current error code (if any)
     * @var string
     */
    protected $errorCode;
    /**
     * An array of error information
     * @var array
     */
    protected $errorInfo;
        
    /**
     * The current HQL query
     * @var string
     */
    public $queryString;
    
    /**
     * A log4php Logger object
     * @var \Logger
     */
    protected $logger;
    
    /**
     * Constructor
     * @param HivePDO $pdo
     */
    public function __construct(HivePDO $pdo) {
        $this->logger = \Logger::getLogger('servicesLogger');
        $this->setPDO($pdo);  
    }
    
    /**
     * Clean up operations to be performed when this object is destroyed
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Sends a query request to hive via the HiveServer2 API. Returns true if the request was successful, false otherwise
     * @param array $input_parameters
     * @return boolean
     */
    public function execute(array $input_parameters = array()) {
        $result = true;

        $query = $this->queryString;
        $request = new \apache\hive\service\cli\thrift\TExecuteStatementReq(array('statement' => $query));

        try {
            /* @var $response \apache\hive\service\cli\thrift\TExecuteStatementResp */
            $response = $this->call('ExecuteStatement', $request);
            $this->operationHandle = $response->operationHandle;
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage(), $e);
            throw $e;
        }
        
        return $result;
    }
    
    /**
     * Fetch the next row from the result set
     * @param int $fetch_style
     * @param int $cursor_orientation
     * @param int $cursor_offset
     * @return array
     */
    public function fetch($fetch_style = null, $cursor_orientation = HivePDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
        $row = array();
        
        // if we don't have a result set yet, or we are at the end of the current one
        if(!$this->hasResultSet() || ($rs = $this->getResultSet()) && !$rs->isEmpty() && is_null($rs->key())) {
            $rs = $this->_fetch(); // fetch the next batch
        }
        
        // check that this set isn't empty
        if(!$rs->isEmpty()) {
            $row = $rs->getRow(); // gets the current row and advances the pointer
        }
        
        return $row;
    }
    
    /**
     * Fetch the entire result set in the format specific to the passed arguments
     * @param int $fetch_style
     * @param int $fetch_argument
     * @param array $ctor_args
     * @return array
     */
    public function fetchAll($fetch_style = null, $fetch_argument = HivePDO::FETCH_ORI_NEXT, array $ctor_args = array()) {
        $fetchedRows = $totalRows = 0;
        $out = array();
        
        while(($rs = $this->_fetch()) && !$rs->isEmpty()) {
            $totalRows += $rs->count();
            
            while(false !== ($row = $rs->current())) {
                $fetchedRows++;
                array_push($out, $row);
                $rs->next();
            }
        }

        return $out;
    }
    
    /**
     * Fetch only a single column from the current row, then advances the pointer to the next row
     * @param int $column_number
     * @return mixed
     */
    public function fetchColumn($column_number = 0) {
        $retVal = false;
        
        if($row = $this->fetch()) {
            $keys = $this->getResultSet()->getKeyMap()->get();
            $retVal = $row[$keys[$column_number]];
        }
        
        return $retVal;
    }
    
    /**
     * Fetch data from the result set, hydrate that data as a class in occordance with the passed arguments
     * @param mixed $class_name
     * @param array $ctor_args
     * @return mixed
     */
    public function fetchObject($class_name = "\stdClass", array $ctor_args = array()) {
        $retVal = false;

        if($row = $this->fetch()) {
            if(!is_object($class_name)) {
                try {
                    $reflector = new \ReflectionClass($class_name);
                    
                    if(count($ctor_args) > 0) {
                        // @codeCoverageIgnoreStart
                        $obj = $reflector->newInstanceArgs($ctor_args);
                    } else { // @codeCoverageIgnoreEnd
                        $obj = $reflector->newInstance();
                    }
                } catch(\Exception $e) {
                    throw $e;
                }
            } else {
                $obj = $class_name;
            }
            
            foreach($row as $key => $value) {
                $obj->{$key} = $value;
            }
            
            $retVal = $obj;
        }
        
        return $retVal;
    }
    
    /**
     * Closes this HivePDOStatement
     * @throws \Examples\ThriftServices\Hive\HivePDOException
     */
    public function close() {
        if($this->isOpen()) {
            $request = new \apache\hive\service\cli\thrift\TCloseOperationReq(array('operationHandle' => $this->operationHandle));
    
            try {
                /* @var $response \apache\hive\service\cli\thrift\TCloseOperationResp */
                $response = $this->call('CloseOperation', $request);
            } catch(\Examples\ThriftServices\Hive\HivePDOException $e) {
                throw $e;
            }
    
            $this->operationHandle = null;
        }
    }
    
    /**
     * Returns a column count for the current result set (if available)
     * @return int
     */
    public function columnCount() {
        /* @var $schema \Examples\ThriftServices\Hive\Meta\Schema */
        return !is_null($schema = $this->schema) ? $schema->getKeyMap()->count() : 0;
    }
    
    /**
     * Get the row count for this object
     * @return int
     * @codeCoverageIgnore
     */
    public function rowCount() {
        return 0;
    }
    
    /**
     * Set the fetch size
     * @param int $size
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     * @codeCoverageIgnore
     */
    public function setFetchSize($size) {
        $this->fetchSize = $size;
        return $this;
    }
    
    /**
     * Get the current fetch size for fetch calls to the HiveServer2 API
     * @return int
     * @codeCoverageIgnore
     */
    public function getFetchSize() {
        return $this->fetchSize;
    }
    
    /**
     * Set the fetch mode used to retrieve data from the current result set
     * @param int $mode
     * @return boolean
     * @codeCoverageIgnore
     */
    public function setFetchMode($mode) {
        $this->fetch_style = $mode;
        return true;
    }

    /**
     * Get any current error code for this statement
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getErrorCode() {
        $this->errorCode;
    }
    
    /**
     * Get the current error info object associated with this statement
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getErrorInfo() {
        return $this->errorInfo;
    }
    
    /**
     * toString implementation
     * @return string
     * @codeCoverageIgnore
     */
    public function __toString() {
        return sprintf(__CLASS__ . " [opType=%s, statement=%s]",
                $this->isOpen() ? \apache\hive\service\cli\thrift\TOperationType::$__names[$this->operationHandle->operationType] : "N/A",
                str_replace(array("\n","\r", "\n\r", "\t"), " ", $this->queryString));
    }
    
    /**
     * Creates an error info array based on the passed TStatus object.
     * @static
     * @param \apache\hive\service\cli\thrift\TStatus $status
     * @return array
     * @codeCoverageIgnore
     */
    protected static function createErrorInfo(\apache\hive\service\cli\thrift\TStatus $status) {
        return array(
                $status->sqlState,
                $status->errorCode,
                $status->errorMessage
        );
    }
    
    /**
     * Invoke a fetch result request via the HiveServer2 API. Hydrates the result set returned with the result set class
     * returned by the getResultSetClass call. If we have rows in the result set and we don't have a schema, send
     * a metadata request via the HiveServer2 API. From the schema, we determine how we can name columns with each row,
     * and how we can retrieve data values from the HiveServer2 result set object 
     * 
     * @return HivePDOResultSet
     * @codeCoverageIgnore
     */
    protected function _fetch() {
        $schema = null;
        $request = new \apache\hive\service\cli\thrift\TFetchResultsReq(array(
                'operationHandle' => $this->operationHandle,
                'orientation' => \apache\hive\service\cli\thrift\TFetchOrientation::FETCH_NEXT,
                'maxRows' => $this->getFetchSize()
            )
        );
    
        /* @var $response \apache\hive\service\cli\thrift\TFetchResultsResp */
        $response = $this->call('FetchResults', $request);
        $rows = $response->results->rows;
    
        $class = $this->getResultSetClass();
        $obj = new $class($rows);
    
        // we have rows
        if(!$obj->isEmpty()) {
            // schema is not currently defined
            if(is_null($schema = $this->schema)) {
                $schema = new Meta\Schema();
                $request = new \apache\hive\service\cli\thrift\TGetResultSetMetadataReq(array('operationHandle' => $this->operationHandle));
    
                /* @var $response \apache\hive\service\cli\thrift\TGetResultSetMetadataResp */
                $response = $this->call('GetResultSetMetadata', $request);
                $schema->setKeyMap(Meta\KeyMap::factory($response->schema))
                    ->setPropertyMap(Meta\PropertyMap::factory(current($rows)));
    
                $this->schema = $schema;
            }
    
            // set the required maps in the result set
            $obj->setKeyMap($schema->getKeyMap())
                ->setPropertyMap($schema->getPropertyMap());
        }
    
        $this->setResultSet($obj);
        return $obj;
    }
    
    /**
     * Check if this statement is currently open
     * @return boolean
     */
    protected function isOpen() {
        return $this->operationHandle !== null;
    }
        
    /**
     * String representing the class used for result set construction
     * @return string
     * @codeCoverageIgnore
     */
    protected function getResultSetClass() {
        return "\Examples\ThriftServices\Hive\HivePDOResultSet";
    }
    
    /**
     * Does this statement have a current result set?
     * @return boolean
     * @codeCoverageIgnore
     */
    protected function hasResultSet() {
        return !is_null($this->resultSet);
    }

    /**
     * Get the current result set for this statement
     * @return \Examples\ThriftServices\Hive\HivePDOResultSet
     * @codeCoverageIgnore
     */
    protected function getResultSet() {
        return $this->resultSet;
    }
    
    /**
     * Set the result set for this statement
     * @param \Examples\ThriftServices\Hive\HivePDOResultSet $set
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     * @codeCoverageIgnore
     */
    protected function setResultSet(\Examples\ThriftServices\Hive\HivePDOResultSet $set) {
        $this->resultSet = $set;
        return $this;
    }
    
    /**
     * 
     * @param int $code
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     * @codeCoverageIgnore
     */
    protected function setErrorCode($code) {
        $this->errorCode = $code;
        return $this;
    }
    
    /**
     * 
     * @param array $info
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     * @codeCoverageIgnore
     */
    protected function setErrorInfo(array $info) {
        $this->errorInfo = $info;
        return $this;
    }
    
    /**
     * 
     * @return \Examples\ThriftServices\Hive\HivePDO
     * @codeCoverageIgnore
     */
    protected function getPDO() {
        return $this->pdo;
    }
    
    /**
     * 
     * @param HivePDO $pdo
     * @return \Examples\ThriftServices\Hive\HivePDOStatement
     * @codeCoverageIgnore
     */
    protected function setPDO(HivePDO $pdo) {
        $this->pdo = $pdo;
        return $this;
    }
    
    /**
     * Make a call to Hive Server 2 using the public thrift API
     * 
     * @param string $fn         The API function we are going to call
     * @param object $request    The request message we are going to send
     * 
     * @throws Exception
     * @throws \Examples\ThriftServices\Hive\HivePDOException
     * 
     * @return mixed             A response message appropriate for the request sent
     * @codeCoverageIgnore
     */
    protected function call($fn, $request) {
        $pdo = $this->getPDO();

        /* @var $t \Thrift\Transport\TTransport */
        if(($t = $pdo->getTransport()) && !$t->isOpen()) {
            $this->logger->debug(sprintf("%s not open, attempting connection", get_class($t)));
            $t->open();
        }
        
        $service = $pdo->getClient();
        $sessions = Session\HiveSessionCollection::getInstance();
    
        if(is_null($session = $sessions->getAt(($username = $pdo->getUsername())))) {
            $session = Session\HiveSession::create($service, $username, $pdo->getPassword());
            $sessions->addAt($session, $username);
        }
        
        try {
            $request->sessionHandle = $session->getHandle();
            $this->logger->debug(sprintf("%s: Sending %s request", $this, $fn));
            
            $method = new \ReflectionMethod($service, $fn);
            $response = $method->invoke($service, $request);
        } catch(\Exception $e) {
            throw new \Examples\ThriftServices\Hive\HivePDOException(sprintf("Error occured whilst executing %s", $fn), $e->getCode(), $e);
        }
        
        // fail, throw exception
        if($response->status->statusCode == \apache\hive\service\cli\thrift\TStatusCode::ERROR_STATUS) {
            $this->setErrorCode($response->status->errorCode)
                ->setErrorInfo(($info = static::createErrorInfo($response->status)));

            throw \Examples\ThriftServices\Hive\HivePDOException::factory($info);
        }
    
        $this->logger->debug(sprintf("%s: Received %s response", $this, get_class($response)));
        return $response;
    }
}