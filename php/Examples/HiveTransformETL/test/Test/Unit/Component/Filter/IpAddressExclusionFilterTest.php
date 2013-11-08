<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter;

class IpAddressExclusionFilterTest extends \PHPUnit_Framework_TestCase {
    public function testFilter() {
        $ips = array(
            '192.168.1.255' => true,
            '10.0.0.1' => true,
            '100.251.0.211' => false
        );
        
        $stub = $this->getMockBuilder("\Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter")
            ->disableOriginalConstructor()
            ->setMethods(array('getRules'))
            ->getMock();
        
        $rule = $this->getMockBuilder("\Examples\HiveTransformETL\Component\Filter\Rules\RegexRule")
            ->disableOriginalConstructor()
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
        
        foreach($ips as $ip => $expected) {
            $this->assertEquals($expected, $stub->filter($ip));
        }
        
        $this->assertTrue($stub->filter('192.168.1.255')); // test cached result
    }
}

return __NAMESPACE__;