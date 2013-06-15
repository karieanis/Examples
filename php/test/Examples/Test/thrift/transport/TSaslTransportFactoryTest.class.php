<?php
namespace Examples\Test\Thrift\Transport;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 */
class TSaslTransportFactoryTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        \Examples\Hive\Service\HiveServer2::register();
    }
    
    public function run(\PHPUnit_Framework_TestResult $result = NULL) {
        $this->setPreserveGlobalState(true);
        $this->setInIsolation(false);
    
        return parent::run($result);
    }
    
    public function testFactoryWithNoSasl() {
        $rawTransport = $this->getMockBuilder('\Thrift\Transport\TSocket')
            ->disableOriginalConstructor()
            ->getMock();
        
        $mechanismName = \Examples\Auth\SASL\Mechanism\BaseMechanism::NOSASL;
        $transport = \Examples\Thrift\Transport\TSaslTransportFactory::factory($rawTransport, $mechanismName, "", "");
        
        $this->assertSame($rawTransport, $transport);
    }
    
    public function testFactoryWithPlainSasl() {
        $username = "test";
        $password = "test";
        
        $rawTransport = $this->getMockBuilder('\Thrift\Transport\TSocket')
            ->disableOriginalConstructor()
            ->getMock();
        
        $mechanismName = \Examples\Auth\SASL\Mechanism\BaseMechanism::PLAIN;
        
        /* @var $transport \Examples\Thrift\Transport\TSaslClientTransport */
        $transport = \Examples\Thrift\Transport\TSaslTransportFactory::factory($rawTransport, $mechanismName, $username, $password);
        $this->assertInstanceOf("\Examples\Thrift\Transport\TSaslClientTransport", $transport);
        
        $clientAccessor = new \ReflectionProperty($transport, "client");
        $clientAccessor->setAccessible(true);
        
        /* @var $client \Examples\Auth\SASL\ClientImpl */
        $client = $clientAccessor->getValue($transport);
        
        /* @var $mechanism \Examples\Auth\SASL\Mechanism\Plain */
        $mechanism = $client->getMechanism();
        $this->assertEquals($username, $mechanism->getUsername());
        $this->assertEquals($username, $mechanism->getPassword());
    }
    
    public function testFactoryWithInvalidSasl() {
        $rawTransport = $this->getMockBuilder('\Thrift\Transport\TSocket')
            ->disableOriginalConstructor()
            ->getMock();
        
        $mechanismName = "fakeSasl";
        $this->setExpectedException("\InvalidArgumentException");
        $transport = \Examples\Thrift\Transport\TSaslTransportFactory::factory($rawTransport, $mechanismName, "", "");
    }
}

return __NAMESPACE__;