<?php
namespace Examples\ThriftServices\Test\Hive\Conf;

class HadoopDatabaseConfFactoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterThrowsExceptionWithInvalidClass() {
        \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::register("\stdClass");
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testRegisterSuccessfully() {
        \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::register("\Examples\ThriftServices\Hive\Conf\HiveConf");
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testFactorySuccessfully() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\Conf\HiveConf")
            ->disableOriginalConstructor()
            ->getMock();
        
        \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::register(get_class($stub));
        $instance = \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::factory();
        $this->assertInstanceOf(get_class($stub), $instance);
    }
    
    /**
     * @runTestInSeperateProcess
     * @expectedException \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactoryException
     */
    public function testFactoryThrowsException() {
        \Examples\ThriftServices\Hadoop\Conf\HadoopDatabaseConfFactory::factory();
    }
}

return __NAMESPACE__;