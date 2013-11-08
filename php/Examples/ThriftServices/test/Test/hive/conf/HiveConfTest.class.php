<?php
namespace Examples\ThriftServices\Test\Hive\Conf;

class HiveConfTest extends \PHPUnit_Framework_TestCase {
    public function testApplyDefaults() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\Conf\HiveConf")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $reflector = new \ReflectionClass($stub);
        
        $method = $reflector->getMethod("applyDefaults");
        $method->setAccessible(true);
        
        $property = $reflector->getProperty("defaults");
        $property->setAccessible(true);
        $defaults = $property->getValue(null);
        
        $method->invoke($stub);
        
        foreach($defaults as $key => $value) {
            $this->assertEquals($value, $stub[$key]);
        }
    }
    
    public function testApplyOverlay() {
        $thriftConfs = array(
            "test" => "value"        
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\Conf\HiveConf")
            ->disableOriginalConstructor()
            ->setMethods(array("getConfig"))
            ->getMock();
        
        $config = $this->getMockBuilder("\Examples\ThriftServices\Util\Config")
            ->disableOriginalConstructor()
            ->getMock();
        
        $config->staticExpects($this->once())
            ->method("get")
            ->with("app>thrift")
            ->will($this->returnValue($thriftConfs));
        
        $stub->expects($this->once())
            ->method("getConfig")
            ->will($this->returnValue($config));
        
        $method = new \ReflectionMethod($stub, "applyOverlay");
        $method->setAccessible(true);
        $method->invoke($stub);
        
        foreach($thriftConfs as $key => $value) {
            $this->assertEquals($value, $stub[$key]);
        }
    }
}

return __NAMESPACE__;