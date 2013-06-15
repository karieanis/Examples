<?php
namespace Examples\Test\Hive\Session;

class HiveSessionCollection extends \PHPUnit_Framework_TestCase {
    public function testGetInstance() {
        $instance1 = \Examples\Hive\Session\HiveSessionCollection::getInstance();
        $instance2 = \Examples\Hive\Session\HiveSessionCollection::getInstance();
        
        $this->assertSame($instance1, $instance2);
    }
    
    public function testDelegatedCallSuccess() {
        $manager = \Examples\Hive\Session\HiveSessionCollection::getInstance();
        $this->assertEquals(0, $manager->count()); // count call delegated to underlying collection object
    }
    
    public function testDelegatedCallThrowsException() {
        $manager = \Examples\Hive\Session\HiveSessionCollection::getInstance();
        $this->setExpectedException("\ReflectionException");
        $manager->nonExistantMethod();
    }
}

return __NAMESPACE__;