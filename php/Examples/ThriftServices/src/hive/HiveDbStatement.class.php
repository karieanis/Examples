<?php
namespace Examples\ThriftServices\Hive;

/**
 * This class exists to ensure compatibility between our HiveServer1 and HiveServer2 interfaces
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HiveDbStatement implements \IHiveDbStatement {
    /**
     * The underlying HivePDOStatement
     * @var HivePDOStatement
     */
    protected $statement;
    
    /**
     * Constructor
     * @param HivePDOStatement $statement
     * @codeCoverageIgnore
     */
    public function __construct(HivePDOStatement $statement) {
        $this->statement = $statement;
        $this->statement->setFetchMode(HivePDO::FETCH_ASSOC);
    }
    
    /**
     * Fetch a number of rows from the underlying statement
     * @param int $noOfRows
     * @return mixed
     */
    public function fetchMultiple($noOfRows = 1) {
        $rows = array();
        $this->statement->setFetchSize($noOfRows);
        
        for($i = 0; $i < $noOfRows && ($row = $this->statement->fetch()); $i++) {
            array_push($rows, $row);
        }
        
        return $rows;
    }
    
    /**
     * Fetch a single row from the underlying statement
     * @return mixed
     * @codeCoverageIgnore
     */
    public function fetch() {
        return $this->statement->fetch();
    }
    
    /**
     * Fetch the data for the first column from the underlying statement
     * @return string
     * @codeCoverageIgnore
     */
    public function fetchColumn() {
        return $this->statement->fetchColumn(0);
    }
    
    /**
     * Retrieve any current error info, return null if there is none
     * @return mixed
     * @codeCoverageIgnore
     */
    public function getErrorMsg() {
        $info = $this->statement->getErrorInfo();
        return isset($info[2]) ? $info[2] : null;
    }

    /**
     * Perform any cleanup actions that may be required
     * @codeCoverageIgnore
     */
    public function __destruct() {
        
    }
}