<?php
namespace Examples\ThriftServices\Test\Hive;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class HivePDOResultSetTest extends \PHPUnit_Framework_TestCase {
    public function testGetRow() {
        $expected = array("test" => true);
        
        $keyMap = new \Examples\ThriftServices\Hive\Meta\KeyMap();
        $keyMap->add("test");
        
        $propertyMap = new \Examples\ThriftServices\Hive\Meta\PropertyMap();
        $propertyMap->add("boolVal");
        
        $boolVal = new \stdClass();
        $boolVal->value = true;
        
        $tColumnValue = new \stdClass();
        $tColumnValue->boolVal = $boolVal;
        
        $tRow = new \stdClass();
        $tRow->colVals = array($tColumnValue);
        
        $stub = new \Examples\ThriftServices\Hive\HivePDOResultSet(array($tRow));
        $stub->setKeyMap($keyMap);
        $stub->setPropertyMap($propertyMap);
        
        $this->assertSame($expected, $stub->getRow());
        $this->assertFalse($stub->getRow());
    }
}

return __NAMESPACE__;