<?php
namespace Examples\Test\Hive\Conf;

class HadoopDatabaseConfFactoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterThrowsExceptionWithInvalidClass() {
        \Examples\Hadoop\Conf\HadoopDatabaseConfFactory::register("\stdClass");
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testRegisterSuccessfully() {
        \Examples\Hadoop\Conf\HadoopDatabaseConfFactory::register("\Examples\Hive\Conf\HiveConf");
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testFactorySuccessfully() {
        $stub = $this->getMockBuilder("\Examples\Hive\Conf\HiveConf")
            ->disableOriginalConstructor()
            ->getMock();
        
        \Examples\Hadoop\Conf\HadoopDatabaseConfFactory::register(get_class($stub));
        $instance = \Examples\Hadoop\Conf\HadoopDatabaseConfFactory::factory();
        $this->assertInstanceOf(get_class($stub), $instance);
    }
    
    /**
     * @runTestInSeperateProcess
     * @expectedException \Examples\Hadoop\Conf\HadoopDatabaseConfFactoryException
     */
    public function testFactoryThrowsException() {
        \Examples\Hadoop\Conf\HadoopDatabaseConfFactory::factory();
    }
}

return __NAMESPACE__;