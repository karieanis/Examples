<?php
namespace Examples\Test\Auth\SASL\Mechanism;

class AnonymousTest extends \PHPUnit_Framework_TestCase {
    public function testProcess() {
        $expected = "Anonymous, None";
        $stub = $this->getMockBuilder("\Examples\Auth\SASL\Mechanism\Anonymous")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $this->assertEquals($expected, $stub->process());
        $this->assertTrue($stub->isComplete());
    }
}

return __NAMESPACE__;