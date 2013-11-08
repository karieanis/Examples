<?php
namespace Examples\ThriftServices\Test\Hive;

/**
 * @runTestsInSeparateProcesses
 */
class HivePDOStatementTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        \Examples\ThriftServices\Hive\Service\HiveServer2::register();
    }

    public function testConstructor() {
        $pdo = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDO")
            ->disableOriginalConstructor()
            ->getMock();
        
        $instance = new \Examples\ThriftServices\Hive\HivePDOStatement($pdo);
        $this->assertInstanceOf("\Examples\ThriftServices\Hive\HivePDOStatement", $instance);
    }
        
    /**
     * 
     * Test the execute method being invoked successfully on HivePDOStatement
     * 
     * We expect
     * - The call method will be invoked once
     * - A \TExecuteStatementResp object with an operation handle will be returned
     * - The method will return true and
     * - The operationHandle for the HivePDOStatement will be equal to the handle of the \TExecuteStatementResp object
     * 
     * 
     * 
     */
    public function testExecuteSuccessfully() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("call"))
            ->getMock();
        
        $stub->queryString = "SELECT * FROM test";
        
        $mockLogger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->getMock();
        
        $property = new \ReflectionProperty($stub, "logger");
        $property->setAccessible(true);
        $property->setValue($stub, $mockLogger);
        
        $response = new \TExecuteStatementResp(array("operationHandle" => "fake"));

        $stub->expects($this->once())
            ->method("call")
            ->will($this->returnValue($response));
        
        $this->assertTrue($stub->execute());
        
        $property = new \ReflectionProperty($stub, "operationHandle");
        $property->setAccessible(true);
        $this->assertEquals("fake", $property->getValue($stub));
    }
    
    /**
     * 
     * Test the execute method failing when invoked on HivePDOStatement
     * 
     * We expect
     * - The call method will be invoked once
     * - An Exception will be thrown
     * - The error method of the logger will be invoked once
     * - The method returns false and
     * - The operationHandle for the HivePDOStatement is null
     */
    public function testExecuteFailure() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("call"))
            ->getMock();
    
        $stub->queryString = "SELECT * FROM test";
    
        $mockLogger = $this->getMockBuilder("\Logger")
            ->disableOriginalConstructor()
            ->setMethods(array("error"))
            ->getMock();
    
        $mockLogger->expects($this->once())
            ->method("error");
        
        $property = new \ReflectionProperty($stub, "logger");
        $property->setAccessible(true);
        $property->setValue($stub, $mockLogger);
    
        $stub->expects($this->once())
            ->method("call")
            ->will($this->returnCallback(
                function() {
                    throw new \Exception("Call failed!");
                }
            )
        );
    
        $this->assertFalse($stub->execute());
        
        $property = new \ReflectionProperty($stub, "operationHandle");
        $property->setAccessible(true);
        $this->assertNull($property->getValue($stub));
    }

    /**
     * 
     * Test that the fetch method returns an empty row
     * 
     * We expect
     * - That hasResultSet returns false
     * - The _fetch method is invoked once
     * - The result set is empty
     * - An empty array is returned
     */
    public function testFetchReturnsEmptyRowWithNoResultSet() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("hasResultSet", "_fetch"))
            ->getMock();
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->getMock();
        
        $rs->expects($this->once())
            ->method("isEmpty")
            ->will($this->returnValue(true));
        
        $stub->expects($this->once())
            ->method("_fetch")
            ->will($this->returnValue($rs));
        
        $stub->expects($this->once())
            ->method("hasResultSet")
            ->will($this->returnValue(false));
        
        $result = $stub->fetch();
        $this->assertEmpty($result);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * 
     * Test that the fetch method returns an empty row
     * 
     * We expect
     * - That hasResultSet returns true
     * - getResultSet returns an instance of HivePDOResultSet
     * - HivePDOResultSet::isEmpty is invoked twice within the fetch method and the first call returns false
     * - HivePDOResultSet::key is invoked once and returns null
     * - _fetch method is invoked once and returns a HivePDOResultSet
     * - The second call to HivePDOResultSet::isEmpty returns true
     * - An empty array is returned
     */
    public function testFetchReturnsEmptyRowWithResultSet() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("hasResultSet", "getResultSet", "_fetch"))
            ->getMock();
        
        $stub->expects($this->once())
            ->method("hasResultSet")
            ->will($this->returnValue(true));
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->getMock();
        
        $rs->expects($this->exactly(2))
            ->method("isEmpty")
            ->will($this->onConsecutiveCalls(false, true));
        
        $rs->expects($this->once())
            ->method("key")
            ->will($this->returnValue(null));

        $stub->expects($this->once())
            ->method("getResultSet")
            ->will($this->returnValue($rs));
        
        $stub->expects($this->once())
            ->method("_fetch")
            ->will($this->returnValue($rs));
        
        $result = $stub->fetch();
        $this->assertEmpty($result);
        $this->assertInternalType('array', $result);
    }
    
    /**
     * 
     * Test that the fetch method returns a single row
     * 
     * We expect
     * - That hasResultSet return false
     * - The _fetch method will be called once, and will return a HivePDOResultSet
     * - HivePDOResultSet::isEmpty will be called once, and will return false
     * - HivePDOResultSet::getRow will be called once, and will return an associative array (single row)
     * - An array with key value pairs is returned
     */
    public function testFetchReturnsSingleRowFromResultSet() {
        $fixture = array(
            "col1" => "value1",
            "col2" => "value2"
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("hasResultSet", "_fetch"))
            ->getMock();
        
        $stub->expects($this->once())
            ->method("hasResultSet")
            ->will($this->returnValue(false));
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->getMock();
        
        $rs->expects($this->once())
            ->method("isEmpty")
            ->will($this->returnValue(false));
        
        $rs->expects($this->once())
            ->method("getRow")
            ->will($this->returnValue($fixture));
        
        $stub->expects($this->once())
            ->method("_fetch")
            ->will($this->returnValue($rs));
        
        $result = $stub->fetch();
        $this->assertEquals(count($fixture), count($result));
        $this->assertInternalType('array', $result);
        $this->assertSame($fixture, $result);
    }
    
    /**
     * 
     * Test that the fetch method returns multiple rows
     * 
     * We expect
     * - That hasResultSet will be called three times, the first of which will return false, then true on subsequent calls
     * - _fetch method will be called twice, both times returning a HivePDOResultSet
     * - That getResultSet will be called twice, both times returning a HivePDOResultSet
     * - HivePDOResultSet::isEmpty will be called five times, the first four times returning false, then finally true
     * - HivePDOResultSet::key will be call twice, first returning an integer, then returning null
     * - HivePDOResultSet::getRow will be called twice, both times return an associative array
     * - An array with key value pairs is returned twice, then finally an empty array is returned ending the while loop
     */
    public function testFetchReturnsMultipleRowsFromResultSet() {
        $fixtures = array(
            array(
                "r1c1" => "v1",
                "r1c2" => "v2"
            ),
            array(
                "r2c1" => "v1",
                "r2c2" => "v2"
            )    
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("getResultSet", "hasResultSet", "_fetch"))
            ->getMock();
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        
        $rs->expects($this->exactly(2))
            ->method("getRow")
            ->will($this->onConsecutiveCalls($fixtures[0], $fixtures[1]));
        
        $stub->expects($this->exactly(3))
            ->method("hasResultSet")
            ->will($this->onConsecutiveCalls(false, true, true));
        
        $stub->expects($this->exactly(2))
            ->method("_fetch")
            ->will($this->returnValue($rs));
        
        $rs->expects($this->exactly(5))
            ->method("isEmpty")
            ->will($this->onConsecutiveCalls(false, false, false, false, true));
        
        $rs->expects($this->exactly(2))
            ->method("key")
            ->will($this->onConsecutiveCalls(1, null));
        
        $stub->expects($this->exactly(2))
            ->method("getResultSet")
            ->will($this->returnValue($rs));
        
        
        $result = array();
        while($row = $stub->fetch()) {
            array_push($result, $row);
        }
        
        $this->assertEquals(count($fixtures), count($result));
        $this->assertInternalType('array', $result);
        $this->assertSame($fixtures, $result);
    }

    /**
     * 
     * Test that the fetchAll method returns multiple rows (fetched in a single batch)
     * 
     * We expect
     * - That _fetch will be called twice, returning a HivePDOResultSet with value first, then an empty HivePDOResultSet
     * - HivePDOResultSet::isEmpty will be called twice, returning false then true
     * - HivePDOResultSet::count will be called once, returning the integer value 2
     * - HivePDOResultSet::current will be called three times, returning each item within the fixture, then false
     * - HivePDOResultSet::next will be called twice
     * - An array of arrays will be returned, matching the defined fixture
     */
    public function testFetchAllWithASingleLoop() {
        $fixtures = array(
            array(
                "r1c1" => "v1",
                "r1c2" => "v2"
            ),
            array(
                "r2c1" => "v1",
                "r2c2" => "v2"
            )    
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("_fetch"))
            ->getMock();
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->setMethods(array("isEmpty", "count", "current", "next"))
            ->getMock();
        
        $rs->expects($this->exactly(2))
            ->method("isEmpty")
            ->will($this->onConsecutiveCalls(false, true));
        
        $rs->expects($this->once())
            ->method("count")
            ->will($this->returnValue(count($fixtures)));
        
        $rs->expects($this->exactly(3))
            ->method("current")
            ->will($this->onConsecutiveCalls($fixtures[0], $fixtures[1], false));
        
        $rs->expects($this->exactly(2))
            ->method("next");
        
        $stub->expects($this->exactly(2))
            ->method("_fetch")
            ->will($this->returnValue($rs));
        
        $result = $stub->fetchAll();
        $this->assertEquals(count($fixtures), count($result));
        $this->assertInternalType('array', $result);
        $this->assertSame($fixtures, $result);
    }
    
    /**
     * 
     * Test that the fetchAll method returns multiple rows (fetched in multiple batches)
     * 
     * We expect
     * - That _fetch will be called three times, twice returning a HivePDOResultSet with a value, then an empty HivePDOResultSet
     * - HivePDOResultSet::isEmpty will be called three times, twice returning false, then returning true
     * - HivePDOResultSet::count will be called twice, returning the integer value 1 both times
     * - HivePDOResultSet::current will be called four times, returning an associate array on the first and third calls, false on all others
     * - HivePDOResultSet::next will be called twice
     * An array of arrays will be returned, matching the defined fixture
     */
    public function testFetchAllWithMultipleLoops() {
        $fixtures = array(
            array(
                "r1c1" => "v1",
                "r1c2" => "v2"
            ),
            array(
                "r2c1" => "v1",
                "r2c2" => "v2"
            )
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("_fetch"))
            ->getMock();
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->setMethods(array("isEmpty", "count", "current", "next"))
            ->getMock();
        
        $rs->expects($this->exactly(3))
            ->method("isEmpty")
            ->will($this->onConsecutiveCalls(false, false, true));
        
        $rs->expects($this->exactly(2))
            ->method("count")
            ->will($this->returnValue(1));
        
        $rs->expects($this->exactly(4))
            ->method("current")
            ->will($this->onConsecutiveCalls($fixtures[0], false, $fixtures[1], false));
        
        $rs->expects($this->exactly(2))
            ->method("next");
        
        $stub->expects($this->exactly(3))
            ->method("_fetch")
            ->will($this->returnValue($rs));
        
        $result = $stub->fetchAll();
        $this->assertEquals(count($fixtures), count($result));
        $this->assertInternalType('array', $result);
        $this->assertSame($fixtures, $result);
    }
    
    /**
     * 
     * Test that the fetchColumn method functions with multiple calls
     * 
     * We expect
     * - That fetch will be called three times, twice returning an associative array with key value pairs, then an empty array
     * - getResultSet will be called twice, each time returning a HivePDOResultSet
     * - HivePDOResultSet::getKeyMap will be called twice, each time returning a KeyMap
     * - KeyMap::get will be called twice, each time returning an numerically indexed array of column names
     * - Each invocation will returning a string
     * 
     * The final result is an array containing the column 0 value for both arrays within fixtures.
     */
    public function testFetchColumnWithMultipleCalls() {
        $fixtures = array(
            array(
                "c1" => "r1v1",
                "c2" => "r1v2"
            ),
            array(
                "c1" => "r2v1",
                "c2" => "r2v2"
            )
        );
        
        $expected = array("r1v1", "r2v1");
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("fetch", "getResultSet"))
            ->getMock();
        
        $rs = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOResultSet")
            ->disableOriginalConstructor()
            ->setMethods(array("getKeyMap"))
            ->getMock();
        
        $map = $this->getMockBuilder("\Examples\ThriftServices\Hive\Meta\KeyMap")
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();
        
        $map->expects($this->exactly(2))
            ->method("get")
            ->will($this->returnValue(array("c1", "c2")));
        
        $rs->expects($this->exactly(2))
            ->method("getKeyMap")
            ->will($this->returnValue($map));
        
        $stub->expects($this->exactly(2))
            ->method("getResultSet")
            ->will($this->returnValue($rs));
        
        $stub->expects($this->exactly(3))
            ->method("fetch")
            ->will($this->onConsecutiveCalls($fixtures[0], $fixtures[1], array()));
        
        $result = array();
        while($col = $stub->fetchColumn(0)) {
            array_push($result, $col);
        }
        
        $this->assertEquals(count($expected), count($result));
        $this->assertSame($expected, $result);
    }

    /**
     * 
     * Test that the fetchObject method functions with multiple calls, and no object instance passed
     * 
     * We expect
     * - That fetch will be called twice, returning our fixture, then returning an empty array
     * - The first call to fetchObject will return an object of stdClass
     *     - it should have two properties, matching the column names for the fixture row
     *     - each property shall contain the value appropriate for the fixture column row
     * - The second call to fetchObject will return false 
     */
    public function testFetchObjectWithMultipleCallsAndNoInstance() {
        $fixture = array(
            "c1" => "r1v1",
            "c2" => "r1v2"
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("fetch"))
            ->getMock();
        
        $stub->expects($this->exactly(2))
            ->method("fetch")
            ->will($this->onConsecutiveCalls($fixture, array()));
        
        $obj = $stub->fetchObject("\stdClass");
        $this->assertInstanceOf("\stdClass", $obj);
        
        foreach($fixture as $col => $value) {
            $this->assertEquals($obj->{$col}, $value);
        }
        
        $this->assertFalse($stub->fetchObject("\stdClass"));
    }
    
    /**
     * 
     * Test that the fetchObject method functions with multiple calls, and with an object instance passed
     * 
     * We expect
     * - That fetch will be called twice, returning our fixture, then returning an empty array
     * - The first call to fetchObject will return the same object which we pass it (stdClass)
     *     - it should have two properties, matching the column names for the fixture row
     *     - each property shall contain the value appropriate for the fixture column row
     * - The second call to fetchObject will return false
     */
    public function testFetchObjectWithMultipleCallsWithInstance() {
        $fixture = array(
            "c1" => "r1v1",
            "c2" => "r1v2"
        );
    
        $wrapper = new \stdClass();
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("fetch"))
            ->getMock();
    
        $stub->expects($this->exactly(2))
            ->method("fetch")
            ->will($this->onConsecutiveCalls($fixture, array()));
    
        $obj = $stub->fetchObject($wrapper);
        $this->assertInstanceOf(get_class($wrapper), $obj);
        $this->assertSame($obj, $wrapper);
    
        foreach($fixture as $col => $value) {
            $this->assertEquals($obj->{$col}, $value);
        }
    
        $this->assertFalse($stub->fetchObject(clone $wrapper));
    }
    
    /**
     * @expectedException \ReflectionException
     */
    public function testFetchObjectWithInvalidObject() {
        $fixture = array(
            "c1" => "r1v1",
            "c2" => "r1v2"
        );
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("fetch"))
            ->getMock();
        
        $stub->expects($this->once())
            ->method("fetch")
            ->will($this->returnValue($fixture));
        
        $stub->fetchObject("FakeObject");
    }

    /**
     * Test the getColumnCount method when no schema is defined
     * - The is_null check on the schema property returns true
     * 
     * The final result is the integer 0
     */
    public function testGetColumnCountWithNoSchema() {
        $expected = 0;
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $this->assertEquals($expected, $stub->columnCount());
    }
    
    /**
     * 
     * @expectedException \Examples\ThriftServices\Hive\HivePDOException
     */
    public function testCloseThrowsException() {
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("isOpen", "call"))
            ->getMock();
        
        $stub->expects($this->once())
            ->method("isOpen")
            ->will($this->returnValue(true));
        
        $stub->expects($this->once())
            ->method("call")
            ->will($this->returnCallback(
                function() {
                    throw new \Examples\ThriftServices\Hive\HivePDOException("Test exception");
                }
            )
        );
        
        $stub->close();
    }
    
    /**
     * 
     * Test the getColumnCount method with a defined schema
     * 
     * We expect
     * - The is_check on the schema property return false
     * - Schema::getKeyMap will be called once, returning a KeyMap
     * - KeyMap::count will be called once
     * 
     * The final result is the integer 10
     */
    public function testGetColumnCountWithSchema() {
        $expected = 10;
        
        $stub = $this->getMockBuilder("\Examples\ThriftServices\Hive\HivePDOStatement")
            ->disableOriginalConstructor()
            ->setMethods(array("__toString"))
            ->getMock();
        
        $schema = $this->getMockBuilder("\Examples\ThriftServices\Hive\Meta\Schema")
            ->disableOriginalConstructor()
            ->setMethods(array("getKeyMap"))
            ->getMock();
        
        $map = $this->getMockBuilder("\Examples\ThriftServices\Hive\Meta\KeyMap")
            ->disableOriginalConstructor()
            ->setMethods(array("count"))
            ->getMock();
        
        $map->expects($this->once())
            ->method("count")
            ->will($this->returnValue($expected));
        
        $schema->expects($this->once())
            ->method("getKeyMap")
            ->will($this->returnValue($map));
        
        $property = new \ReflectionProperty($stub, "schema");
        $property->setAccessible(true);
        $property->setValue($stub, $schema);
        
        $this->assertEquals($expected, $stub->columnCount());
    }
}

return __NAMESPACE__;