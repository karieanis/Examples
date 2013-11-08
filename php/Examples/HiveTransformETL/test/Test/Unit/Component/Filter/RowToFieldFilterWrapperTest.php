<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Filter;

class RowToFieldFilterWrapperTest extends \PHPUnit_Framework_TestCase {
    public function testFilter() {
        $row = array(
            'field' => 'value'        
        );
        
        $innerFilter = $this->getMockBuilder("\Examples\HiveTransformETL\Component\Filter\IpAddressExclusionFilter")
            ->disableOriginalConstructor()
            ->getMock();
        
        $innerFilter->expects($this->once())
            ->method('filter')
            ->with('value')
            ->will($this->returnValue(true));
        
        $outerFilter = \Examples\HiveTransformETL\Component\Filter\RowToFieldFilterWrapper::instance();
        $outerFilter->setFieldName('field')
            ->setInnerFilter($innerFilter);
        
        $this->assertTrue($outerFilter->filter($row));
    }
}

return __NAMESPACE__;