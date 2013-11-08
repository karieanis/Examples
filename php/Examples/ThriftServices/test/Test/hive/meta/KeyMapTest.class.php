<?php
namespace Examples\ThriftServices\Test\Hive\Meta;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 */
class KeyMapTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        \Examples\ThriftServices\Hive\Service\HiveServer2::register();
    }
    
    public function run(\PHPUnit_Framework_TestResult $result = NULL) {
        $this->setPreserveGlobalState(true);
        $this->setInIsolation(false);
    
        return parent::run($result);
    }
    
    public function testFactory() {
        $colDesc = new \TColumnDesc();
        $colDesc->columnName = "test";
        
        $schema = new \TTableSchema();
        $schema->columns = array($colDesc);
        
        $map = \Examples\ThriftServices\Hive\Meta\KeyMap::factory($schema);
        $this->assertSame(array("test"), $map->get());
    }
}

return __NAMESPACE__;