<?php
namespace Examples\Test\Hadoop\Service;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 *
 */
class HadoopDatabaseServiceTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException \Examples\Hadoop\Service\HadoopDatabaseServiceException
     * @expectedExceptionMessage Examples\Hive\Service\HiveServer2 is currently registered as the running hive service and cannot be changed 
     */
    public function testRegisterThrowsException() {
        \Examples\Hive\Service\HiveServer2::register();
        \Examples\Hive\Service\HiveServer::register(); // will throw an exception
    }
}

return __NAMESPACE__;