<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter;

class UserAgentInclusionFilterTest extends \PHPUnit_Framework_TestCase {
    public function testFilter() {
        $uas = array(
            "Fake UA1" => true,
            "Fake UA2" => true,
            "Fake UA3" => false
        );
        
        $stub = $this->getMockBuilder("\Examples\HiveTransformETL\Component\Filter\UserAgentInclusionFilter")
            ->disableOriginalConstructor()
            ->setMethods(array('getRules'))
            ->getMock();
        
        $rule = $this->getMockBuilder("\Examples\HiveTransformETL\Component\Filter\Rules\UserAgentWhitelistRule")
            ->disableOriginalConstructor()
            ->setMethods(array('apply'))
            ->getMock();
        
        $rule->expects($this->exactly(5))
            ->method('apply')
            ->will($this->onConsecutiveCalls(
                        true,
                        false, true,
                        false, false
                    )
            );
        
        $stub->expects($this->exactly(3))
            ->method('getRules')
            ->will($this->onConsecutiveCalls(
                        array($rule),
                        array($rule, $rule),
                        array($rule, $rule)
                    )
            );
        
        
        foreach($uas as $ua => $expected) {
            $this->assertEquals(
                    $expected, 
                    ($actual = $stub->filter($ua)), 
                    sprintf("Tested %s, expected %s - got %s", $ua, $expected, $actual)
            );
        }
        
        $this->assertTrue($stub->filter("Fake UA1")); // test cached result
    }
}

return __NAMESPACE__;