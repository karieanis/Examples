<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter;

class InvertingFilterWrapperTest extends \PHPUnit_Framework_TestCase {
    public function testFilter() {
        $innerFilter = $this->getMockBuilder("\Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter")
            ->disableOriginalConstructor()
            ->getMock();
        
        $innerFilter->expects($this->once())
            ->method('filter')
            ->with("192.168.1.1")
            ->will($this->returnValue(true));
        
        $outerFilter = \Examples\HiveTransformETL\Component\Filter\InvertingFilterWrapper::instance();
        $outerFilter->setInnerFilter(
            \Examples\HiveTransformETL\Component\Filter\RowToFieldFilterWrapper::instance()->setFieldName("ip")->setInnerFilter($innerFilter)        
        );
        
        $this->assertFalse($outerFilter->filter(array("ip" => "192.168.1.1")));
    }
}

return __NAMESPACE__;