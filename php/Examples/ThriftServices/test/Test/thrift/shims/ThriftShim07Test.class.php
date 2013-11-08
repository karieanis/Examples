<?php
namespace Examples\ThriftServices\Test\Thrift\Shims;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 *
 */
class ThriftShim07Test extends \PHPUnit_Framework_TestCase {
    protected $BaseConf = array(
        "host" => "test.Examples\ThriftServices.net",
        "port" => 10000,
        "receive_timeout" => 100,
        "send_timeout" => 100,
        "debug" => false        
    );
    
    protected $shim;
    protected $conf;
    
    protected function setUp() {
        \Examples\ThriftServices\Thrift\Shims\ThriftShim07::register();
        $this->shim = \Examples\ThriftServices\Thrift\Shims\ThriftShim::getShim();

        $this->conf = $this->getMockBuilder("\Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf")
            ->getMockForAbstractClass();
        
        foreach($this->BaseConf as $key => $value) {
            $this->conf[$key] = $value;
        }
    }
    
    protected function tearDown() {
        unset($this->shim, $this->conf);
    }
    
    /**
     * 
     * @return \Examples\ThriftServices\Thrift\Shims\ThriftShim07
     */
    protected function getShim() {
        return $this->shim;
    }
    
    /**
     * 
     * @return \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConf
     */
    protected function getConf() {
        return $this->conf;
    }
    
    public function testGetTransportWithTSocket() {
        $class = "TSocket";
        $shim = $this->getShim();
        
        $transport = $shim->getTransport($class, $this->getConf());
        $this->assertInstanceOf($shim::NAMESPACE_TRANSPORT . $class, $transport);
    }
    
    public function testGetTransportWithTSocketPool() {
        $class = "TSocketPool";
        $shim = $this->getShim();
        $conf = $this->getConf();
        
        $conf['port'] = array($conf['port']);
        $conf['host'] = array($conf['host']);
    
        $transport = $shim->getTransport($class, $conf);
        $this->assertInstanceOf($shim::NAMESPACE_TRANSPORT . $class, $transport);
    }
    
    /**
     * @expectedException \Examples\ThriftServices\Thrift\Shims\ThriftShimException
     */
    public function testGetTransportThrowsException() {
        $class = "FakeClass";
        $shim = $this->getShim();
        $shim->getTransport($class, $this->getConf());
    }
    
    public function testGetTBinaryProtocolAcceleratedReturnsNonAcceleratedProtocol() {
        $class = "TBinaryProtocolAccelerated";
        $shim = $this->getShim();
        
        $transport = $this->getMockBuilder("\TTransport")
            ->getMockForAbstractClass();
        
        $protocol = $shim->getProtocol($class, $transport);
        $this->assertNotInstanceOf($shim::NAMESPACE_PROTOCOL . $class, $protocol);
    }
    
    public function testGetProtocolWithTBinaryProtocol() {
        $class = "TBinaryProtocol";
        $shim = $this->getShim();
        
        $transport = $this->getMockBuilder("\TTransport")
            ->getMockForAbstractClass();
        
        $protocol = $shim->getProtocol($class, $transport);
        $this->assertInstanceOf($shim::NAMESPACE_PROTOCOL . $class, $protocol);
    }

    /**
     * @expectedException \Examples\ThriftServices\Thrift\Shims\ThriftShimException
     */
    public function testGetProtocolThrowsException() {
        $class = "FakeClass";
        $shim = $this->getShim();
    
        $transport = $this->getMockBuilder($shim::NAMESPACE_TRANSPORT . "TTransport")
            ->getMockForAbstractClass();
    
        $shim->getProtocol($class, $transport);
    }
    
    public function testSecondRegisterThrowsException() {
        $this->setExpectedException(
            "\Examples\ThriftServices\Thrift\Shims\ThriftShimException",
            "Examples\ThriftServices\Thrift\Shims\ThriftShim07 is currently registered as the running thrift shim and cannot be changed"
        );
        
        \Examples\ThriftServices\Thrift\Shims\ThriftShim07::register();
    }
}

return __NAMESPACE__;