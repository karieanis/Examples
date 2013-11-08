<?php
namespace Examples\ThriftServices\Test\Auth\SASL\Mechanism;

class FactoryTest extends \PHPUnit_Framework_TestCase {
    public function testFactoryWithRecognisedMechanism() {
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->getMock();
        
        $type = \Examples\ThriftServices\Auth\SASL\Mechanism\BaseMechanism::PLAIN;
        $mechanism = \Examples\ThriftServices\Auth\SASL\Mechanism\Factory::factory($type, $client);
        $this->assertInstanceOf("\Examples\ThriftServices\Auth\SASL\Mechanism\Plain", $mechanism);
    }
    
    public function testFactoryWithUnknownMechanism() {
        $client = $this->getMockBuilder("\Examples\ThriftServices\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->getMock();
        
        $type = "fakeMechanism";
        $this->assertNull(\Examples\ThriftServices\Auth\SASL\Mechanism\Factory::factory($type, $client));
    }
}

return __NAMESPACE__;