<?php
namespace Examples\ThriftServices\Test\Thrift\Transport;

/**
 * Unit testing for TSaslTransport
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 */
class TSaslTransportTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        \Examples\ThriftServices\Hive\Service\HiveServer2::register();
    }
    
    public function run(\PHPUnit_Framework_TestResult $result = NULL) {
        $this->setPreserveGlobalState(true);
        $this->setInIsolation(false);
    
        return parent::run($result);
    }
    
    public function testReceiveSaslMessageCompleteWithNoPayload() {
        // complete status byte, payload length bytes indicating 0 byte payload length
        $header = pack("CN", \Examples\ThriftServices\Thrift\Transport\TSaslTransport::COMPLETE, 0);
        
        $logger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->setMethods(array("debug"))
            ->getMock();
        
        $logger->expects($this->once())
            ->method("debug");
        
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("readAll"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("readAll")
            ->will($this->returnValue($header));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport))
            ->setMethods(array("getRole"))
            ->getMockForAbstractClass();
        
        $saslTransport->expects($this->once())
            ->method("getRole")
            ->will($this->returnValue($saslTransport::ROLE_CLIENT));
        
        $loggerAccessor = new \ReflectionProperty($saslTransport, "logger");
        $loggerAccessor->setAccessible(true);
        $loggerAccessor->setValue($saslTransport, $logger);
        
        $method = new \ReflectionMethod($saslTransport, "receiveSaslMessage");
        $method->setAccessible(true);
        
        /* @var $response \Examples\ThriftServices\Auth\SASL\Response */
        $response = $method->invoke($saslTransport);
        $this->assertInstanceOf("\Examples\ThriftServices\Auth\SASL\Response", $response);
        $this->assertEquals($saslTransport::COMPLETE, $response->getStatus());
        $this->assertEmpty($response->getPayload());
    }
    
    public function testReceiveSaslMessageWithNoStatusThrowsException() {
        // null byte status, zero length payload
        $header = pack("xN", 0);

        $logger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->setMethods(array("debug"))
            ->getMock();
        
        $logger->expects($this->once())
            ->method("debug");
        
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("readAll", "write", "flush"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("readAll")
            ->will($this->returnValue($header));
        
        $rawTransport->expects($this->once())
            ->method("write");
        
        $rawTransport->expects($this->once())
            ->method("flush");
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport))
            ->setMethods(array("getRole"))
            ->getMockForAbstractClass();
        
        $saslTransport->expects($this->once())
            ->method("getRole")
            ->will($this->returnValue($saslTransport::ROLE_CLIENT));
        
        $loggerAccessor = new \ReflectionProperty($saslTransport, "logger");
        $loggerAccessor->setAccessible(true);
        $loggerAccessor->setValue($saslTransport, $logger);
        
        $method = new \ReflectionMethod($saslTransport, "receiveSaslMessage");
        $method->setAccessible(true);
        
        $this->setExpectedException("\Thrift\Exception\TTransportException");
        $method->invoke($saslTransport);
    }
    
    public function testReceiveSaslMessageWithErrorStatusThrowsException() {
        // bad status byte, payload length bytes indicating 40 byte payload length
        $header = pack("CN", \Examples\ThriftServices\Thrift\Transport\TSaslTransport::BAD, 40);

        $logger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->setMethods(array("debug"))
            ->getMock();
        
        $logger->expects($this->never())
            ->method("debug");
        
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("readAll"))
            ->getMock();
        
        $rawTransport->expects($this->exactly(2))
            ->method("readAll")
            ->will($this->onConsecutiveCalls($header, ""));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport))
            ->getMockForAbstractClass();
        
        $loggerAccessor = new \ReflectionProperty($saslTransport, "logger");
        $loggerAccessor->setAccessible(true);
        $loggerAccessor->setValue($saslTransport, $logger);
        
        $method = new \ReflectionMethod($saslTransport, "receiveSaslMessage");
        $method->setAccessible(true);
        
        $this->setExpectedException("\Thrift\Exception\TTransportException");
        $method->invoke($saslTransport);
    }

    public function testReadFrameWithNonNegativeLength() {
        $header = pack("N", $length = 10);
        $content = "tencharact"; 
        
        $logger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->setMethods(array("debug"))
            ->getMock();
        
        $logger->expects($this->once())
            ->method("debug");
        
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("readAll"))
            ->getMock();
        
        $rawTransport->expects($this->exactly(2))
            ->method("readAll")
            ->will($this->onConsecutiveCalls($header, $content));
        
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->setMethods(array("unwrap"))
            ->getMock();
        
        $client->expects($this->once())
            ->method("unwrap")
            ->will($this->returnCallback(
                    function($encoded) {
                        return $encoded;
                    }
                )
            );
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport, $client))
            ->setMethods(array("getRole"))
            ->getMockForAbstractClass();
        
        $saslTransport->expects($this->once())
            ->method("getRole")
            ->will($this->returnValue($saslTransport::ROLE_CLIENT));
        
        $loggerAccessor = new \ReflectionProperty($saslTransport, "logger");
        $loggerAccessor->setAccessible(true);
        $loggerAccessor->setValue($saslTransport, $logger);

        $saslTransport->readFrame();
        
        $bufferAccessor = new \ReflectionProperty($saslTransport, "readBuffer");
        $bufferAccessor->setAccessible(true);
        
        /* @var $buffer \Thrift\Transport\TMemoryBuffer */
        $buffer = $bufferAccessor->getValue($saslTransport);
        $buffered = $buffer->read($length);
        
        $this->assertSame($content, $buffered);
    }
    
    public function testIsOpenWithUnopenedTransport() {
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(false));
        
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->setMethods(array("isComplete"))
            ->getMock();
        
        $client->expects($this->never())
            ->method("isComplete");
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport, $client))
            ->getMockForAbstractClass();
        
        $this->assertFalse($saslTransport->isOpen());
    }
    
    public function testIsOpenWithIncompleteClient() {
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(true));
        
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->setMethods(array("isComplete"))
            ->getMock();
        
        $client->expects($this->once())
            ->method("isComplete")
            ->will($this->returnValue(false));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport, $client))
            ->getMockForAbstractClass();
        
        $this->assertFalse($saslTransport->isOpen());
    }
    
    public function testIsOpen() {
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(true));
        
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->setMethods(array("isComplete"))
            ->getMock();
        
        $client->expects($this->once())
            ->method("isComplete")
            ->will($this->returnValue(true));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport, $client))
            ->getMockForAbstractClass();
        
        $this->assertTrue($saslTransport->isOpen());
    }

    public function testReadWithIsOpenIsFalseThrowsException() {
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(false));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport))
            ->getMockForAbstractClass();
        
        $this->setExpectedException("\Thrift\Exception\TTransportException");
        $saslTransport->read(1);
    }
    
    public function testReadWithAvailableBuffer() {
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(true));
        
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->setMethods(array("isComplete"))
            ->getMock();
        
        $client->expects($this->once())
            ->method("isComplete")
            ->will($this->returnValue(true));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport, $client))
            ->getMockForAbstractClass();
        
        $bufferAccessor = new \ReflectionProperty($saslTransport, "readBuffer");
        $bufferAccessor->setAccessible(true);
        
        /* @var $buffer \Thrift\Transport\TMemoryBuffer */
        $buffer = $bufferAccessor->getValue($saslTransport);
        $buffer->write($expected = "junk data");
        
        $this->assertSame($expected, $saslTransport->read(strlen($expected)));
    }

    public function testWriteWithIsOpenFalseThrowsException() {
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen"))
            ->getMock();
        
        $rawTransport->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(false));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport))
            ->getMockForAbstractClass();
        
        $this->setExpectedException("\Thrift\Exception\TTransportException");
        $saslTransport->write("data");
    }

    public function testOpenWithCompletedClient() {
        $logger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->setMethods(array("debug"))
            ->getMock();
        
        $logger->expects($this->once())
            ->method("debug");
        
        $rawTransport = $this->getMockBuilder("\Thrift\Transport\TSocket")
            ->disableOriginalConstructor()
            ->getMock();
        
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->setMethods(array("isComplete"))
            ->getMock();
        
        $client->expects($this->once())
            ->method("isComplete")
            ->will($this->returnValue(true));
        
        $saslTransport = $this->getMockBuilder("\Examples\ThriftServices\Thrift\Transport\TSaslTransport")
            ->setConstructorArgs(array($rawTransport, $client))
            ->getMockForAbstractClass();
        
        $loggerAccessor = new \ReflectionProperty($saslTransport, "logger");
        $loggerAccessor->setAccessible(true);
        $loggerAccessor->setValue($saslTransport, $logger);
        
        $this->setExpectedException("\Thrift\Exception\TTransportException");
        $saslTransport->open();
    }
}

return __NAMESPACE__;