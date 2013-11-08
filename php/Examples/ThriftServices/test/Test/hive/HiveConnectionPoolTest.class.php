<?php
namespace Examples\ThriftServices\Test\Hive;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 */
class HiveConnectionPoolTest extends \PHPUnit_Framework_TestCase {
    protected $instance;
    protected $reflector;
    
    protected $BaseConfig = array(
        "host" =>     "teamtest-db-20.eng",
        "port" =>     10000,
        "database" => "em_raw"
    );

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp() {
        \Examples\ThriftServices\Hive\Service\HiveServer::register();
        
        $this->BaseConfig['class'] = \Examples\ThriftServices\Hive\Service\HiveServer::getRunningService()->getConnectionClass();
        $this->instance = \Examples\ThriftServices\Hive\HiveConnectionPool::getInstance();
        $this->reflector = new \ReflectionClass($this->instance);
    }
    
    /**
     * Tests that the connection pool will create a new connection and add that new connection to the pool
     */
    public function testGetNewConnection() {
        $pool = $this->reflector->getProperty("pool");
        $pool->setAccessible(true);
        
        $this->assertCount(0, $pool->getValue($this->instance));
        
        $connection = $this->instance->getConnection(
            $this->BaseConfig['class'], 
            $this->BaseConfig['host'], 
            $this->BaseConfig['port'], 
            $this->BaseConfig['database']
        );
        
        $this->assertInstanceOf($this->BaseConfig['class'], $connection);
        $this->assertCount(1, $pool->getValue($this->instance));
    }
    
    /**
     * Tests that the connection pool will return an existing connection when a cached connection is found
     * in the current pool
     */
    public function testGetExistingConnection() {
        $pool = $this->reflector->getProperty("pool");
        $pool->setAccessible(true);
        
        $FirstConnection = $this->instance->getConnection(
            $this->BaseConfig['class'],
            $this->BaseConfig['host'],
            $this->BaseConfig['port'],
            $this->BaseConfig['database']
        );
        
        $SecondConnection = $this->instance->getConnection(
            $this->BaseConfig['class'],
            $this->BaseConfig['host'],
            $this->BaseConfig['port'],
            $this->BaseConfig['database']
        );
        
        $this->assertSame($FirstConnection, $SecondConnection);
        $this->assertCount(1, $pool->getValue($this->instance));
    }
}

return __NAMESPACE__;