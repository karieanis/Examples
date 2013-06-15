<?php
namespace Examples\Test\Hive\Meta;

/**
 * 
 * @author Jeremy Rayner <jeremy@davros.com.au>
 * @runTestsInSeparateProcesses
 */
class PropertyMapTest extends \PHPUnit_Framework_TestCase {
    protected function setUp() {
        \Examples\Hive\Service\HiveServer2::register();
    }
    
    public function run(\PHPUnit_Framework_TestResult $result = NULL) {
        $this->setPreserveGlobalState(true);
        $this->setInIsolation(false);
    
        return parent::run($result);
    }
    
    public function testFactory() {
        $boolVal = new \TBoolValue();
        $boolVal->value = true;
        
        $tColumnValue = new \TColumnValue();
        $tColumnValue->boolVal = $boolVal;
        
        $tRow = new \TRow();
        $tRow->colVals = array($tColumnValue);
        
        $map = \Examples\Hive\Meta\PropertyMap::factory($tRow);
        $this->assertSame(array(0 => 'boolVal'), $map->get());
    }
}

return __NAMESPACE__;