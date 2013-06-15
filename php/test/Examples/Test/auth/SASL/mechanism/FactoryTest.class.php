<?php
namespace Examples\Test\Auth\SASL\Mechanism;

class FactoryTest extends \PHPUnit_Framework_TestCase {
    public function testFactoryWithRecognisedMechanism() {
        $client = $this->getMockBuilder("\Examples\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->getMock();
        
        $type = \Examples\Auth\SASL\Mechanism\BaseMechanism::PLAIN;
        $mechanism = \Examples\Auth\SASL\Mechanism\Factory::factory($type, $client);
        $this->assertInstanceOf("\Examples\Auth\SASL\Mechanism\Plain", $mechanism);
    }
    
    public function testFactoryWithUnknownMechanism() {
        $client = $this->getMockBuilder("\Examples\Auth\SASL\ClientImpl")
            ->disableOriginalConstructor()
            ->getMock();
        
        $type = "fakeMechanism";
        $this->assertNull(\Examples\Auth\SASL\Mechanism\Factory::factory($type, $client));
    }
}

return __NAMESPACE__;