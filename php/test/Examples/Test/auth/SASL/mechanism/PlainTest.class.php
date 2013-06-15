<?php
namespace Examples\Test\Auth\SASL\Mechanism;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class PlainTest extends \PHPUnit_Framework_TestCase {
    public function testProcess() {
        $expected = "\x00anonymous\x00anonymous";
        
        $stub = $this->getMockBuilder("\Examples\Auth\SASL\Mechanism\Plain")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $this->assertEquals($expected, $stub->process());
        $this->assertTrue($stub->isComplete());
    }
    
    public function testGetUsernameWithNullProperty() {
        $expected = "anonymous";
        
        $stub = $this->getMockBuilder("\Examples\Auth\SASL\Mechanism\Plain")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $this->assertEquals($expected, $stub->getUsername());
    }
    
    public function testGetUsernameWithProperty() {
        $expected = "hive";
        
        $stub = $this->getMockBuilder("\Examples\Auth\SASL\Mechanism\Plain")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $property = new \ReflectionProperty($stub, "username");
        $property->setAccessible(true);
        $property->setValue($stub, $expected);
        
        $this->assertEquals($expected, $stub->getUsername());
    }
    
    public function testGetPasswordWithNullProperty() {
        $expected = "anonymous";
        
        $stub = $this->getMockBuilder("\Examples\Auth\SASL\Mechanism\Plain")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $this->assertEquals($expected, $stub->getPassword());
    }
    
    public function testGetPasswordWithProperty() {
        $expected = "testing";
        
        $stub = $this->getMockBuilder("\Examples\Auth\SASL\Mechanism\Plain")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $property = new \ReflectionProperty($stub, "password");
        $property->setAccessible(true);
        $property->setValue($stub, $expected);
        
        $this->assertEquals($expected, $stub->getPassword());
    }
}

return __NAMESPACE__;