<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Serializer;

use \Examples\HiveTransformETL\Component\Serializer\TokenizedSerializer;

class TokenizedSerializerTest extends \PHPUnit_Framework_TestCase {
    public function testSerialize() {
        $cases = array(
            "\t" => array("test\ttest\ttest" => array("test", "test", "test")),
            "," => array("test,test,test" => array("test", "test", "test"))
        );
        
        foreach($cases as $delimiter => $case) {
            $serializer = TokenizedSerializer::instance()->setToken($delimiter);
            
            $input = current($case);
            $expected = key($case);
            
            $this->assertSame($expected, $serializer->serialize($input));
        } 
    }
}

return __NAMESPACE__;