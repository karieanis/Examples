<?php 
namespace Examples\Test\Thrift\Service;

/**
 * Tests covering multiple scenarios regarding the use of \Examples\Thrift\Service\Factory
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 */
class FactoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * Base configuration
     * @var array
     */
    protected $BaseConf = array(
        "host" => "",
        "port" => "",
        "receive_timeout" => 10,
        "send_timeout" => 10,
        "debug" => true,
        "auth_mechanism" => "",
        "username" => "hive",
        "password" => ""
    );
    
    protected $configuration;
    
    /**
     * 
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp() {
        $this->configuration = $this->getMockBuilder("\Examples\Hive\Conf\HiveConf")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        foreach($this->BaseConf as $key => $value) {
            $this->configuration[$key] = $value;
        }
        
        \Examples\Hive\Service\HiveServer2::register();
    }
    
    /**
     * 
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown() {
        unset($this->configuration);
    }

    /**
     * Test the factory with a config specifying a single host with multiple ports
     * 
     * We expect
     * - The transport will be TSocketPool
     * - The TSocketPool instance will contain three entries in the server_ array property
     * - Each entry will contain a 'host' key / value pair of the single host
     * - Each entry will contain a 'port' key / value pair, with the value being one of each of the specified ports 
     */
    public function testFactoryWithSingleHostAndMutiplePorts() {
        $test = $this->configuration;
        $test['host'] = "localhost";
        $test['port'] = "10000,20000,30000";
        $test['auth_mechanism'] = \Examples\Auth\SASL\Mechanism\BaseMechanism::NOSASL;
        
        $service = \Examples\Thrift\Service\Factory::factory($test);
        
        $this->assertInstanceOf("\Examples\Thrift\Service\Container", $service);
        $this->assertInstanceOf("\Thrift\Transport\TSocketPool", ($transport = $service->getTransport()));
        
        $property = new \ReflectionProperty($transport, "servers_");
        $property->setAccessible(true);

        $this->assertEquals(3, count($property->getValue($transport)));
        $this->assertSame(
                array(
                        array('host' => "localhost", 'port' => "10000"),
                        array('host' => "localhost", 'port' => "20000"),
                        array('host' => "localhost", 'port' => "30000")
                ), 
                $property->getValue($transport)
        );
    }
    
    /**
     * Test the factory with a config specifying multiple hosts with a single port
     * 
     * We expect
     * - The transport will be TSocketPool
     * - The TSocketPool instance will contain two entries in the server_ array property
     * - Each entry will contain a 'host' key / value pair, with the value being one of each of the specified hosts 
     * - Each entry will contain a 'port' key / value pair of the single port
     */
    public function testFactoryWithMultipleHostsAndSinglePort() {
        $test = $this->configuration;
        $test['host'] = "server1,server2";
        $test['port'] = 10000;
        $test['auth_mechanism'] = \Examples\Auth\SASL\Mechanism\BaseMechanism::NOSASL;
    
        $service = \Examples\Thrift\Service\Factory::factory($test);
    
        $this->assertInstanceOf("\Examples\Thrift\Service\Container", $service);
        $this->assertInstanceOf("\Thrift\Transport\TSocketPool", ($transport = $service->getTransport()));
    
        $property = new \ReflectionProperty($transport, "servers_");
        $property->setAccessible(true);
    
        $this->assertEquals(2, count($property->getValue($transport)));
        $this->assertSame(
                array(
                        array('host' => "server1", 'port' => "10000"),
                        array('host' => "server2", 'port' => "10000"),
                ),
                $property->getValue($transport)
        );
    }
    
    /**
     * Test the factory with a config specifying multiple hosts with multiple ports
     * 
     * We expect
     * - The transport will be TSocketPool
     * - The TSocketPool instance will contain two entries in the server_ array property
     * - Each entry will contain a 'host' key / value pair, with the value being one of each of the specified hosts 
     * - Each entry will contain a 'port' key / value pair, with the value being one of each of the specified ports
     */
    public function testFactoryWithMultipleHostsAndMultiplePorts() {
        $test = $this->configuration;
        $test['host'] = "server1,server2";
        $test['port'] = "10000,20000";
        $test['auth_mechanism'] = \Examples\Auth\SASL\Mechanism\BaseMechanism::NOSASL;
    
        $service = \Examples\Thrift\Service\Factory::factory($test);
    
        $this->assertInstanceOf("\Examples\Thrift\Service\Container", $service);
        $this->assertInstanceOf("\Thrift\Transport\TSocketPool", ($transport = $service->getTransport()));
    
        $property = new \ReflectionProperty($transport, "servers_");
        $property->setAccessible(true);
    
        $this->assertEquals(2, count($property->getValue($transport)));
        $this->assertSame(
                array(
                        array('host' => "server1", 'port' => "10000"),
                        array('host' => "server2", 'port' => "20000"),
                ),
                $property->getValue($transport)
        );
    }
    
    /**
     * Test the factory with a config specifying a single host and port
     * 
     * We expect
     * - The transport will be TSocket
     */
    public function testFactoryWithSingleHostAndSinglePort() {
        $test = $this->configuration;
        $test['host'] = "server1";
        $test['port'] = "10000";
        $test['auth_mechanism'] = \Examples\Auth\SASL\Mechanism\BaseMechanism::NOSASL;
    
        $service = \Examples\Thrift\Service\Factory::factory($test);
    
        $this->assertInstanceOf("\Examples\Thrift\Service\Container", $service);
        $this->assertInstanceOf("\Thrift\Transport\TSocket", ($transport = $service->getTransport()));
    }
    
    /**
     * Test the factory with a config specifying a single host and port with SASL authentication
     * 
     * We expect
     * - The transport will be TSaslClientTransport
     */
    public function testFactoryWithSingleHostAndSinglePortAndSASLAuth() {
        $test = $this->configuration;
        $test['host'] = "server1";
        $test['port'] = "10000";
        $test['auth_mechanism'] = \Examples\Auth\SASL\Mechanism\BaseMechanism::PLAIN;
    
        $service = \Examples\Thrift\Service\Factory::factory($test);
    
        $this->assertInstanceOf("\Examples\Thrift\Service\Container", $service);
        $this->assertInstanceOf("\Examples\Thrift\Transport\TSaslClientTransport", ($transport = $service->getTransport()));
    }
    
    /**
     * Test the factory with a config specifying multiple hosts and multiple ports
     * 
     * We expect
     * - The transport will be TSaslClientTransport
     */
    public function testFactoryWithMultipleHostsAndMultiplePortsAndSASLAuth() {
        $test = $this->configuration;
        $test['host'] = "server1,server2";
        $test['port'] = "10000,20000";
        $test['auth_mechanism'] = \Examples\Auth\SASL\Mechanism\BaseMechanism::PLAIN;
    
        $service = \Examples\Thrift\Service\Factory::factory($test);
    
        $this->assertInstanceOf("\Examples\Thrift\Service\Container", $service);
        $this->assertInstanceOf("\Examples\Thrift\Transport\TSaslClientTransport", ($transport = $service->getTransport()));
    }
}

return __NAMESPACE__;