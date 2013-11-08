<?php
namespace Examples\HiveTransformETL\Test\Unit\Component\Deserializer;

use \Examples\HiveTransformETL\Component\Deserializer\TokenizedDeserializer;

class TokenizedDeserializerTest extends \PHPUnit_Framework_TestCase {
    public function testDeserialize() {
        $cases = array(
            "\t" => array("test\ttest\ttest" => array("test", "test", "test")),
            "," => array("test,test,test" => array("test", "test", "test"))
        );
        
        foreach($cases as $delimiter => $case) {
            $deserializer = TokenizedDeserializer::instance()->setToken($delimiter);
            
            $input = key($case);
            $expected = current($case);
            
            $this->assertSame($expected, $deserializer->deserialize($input));
        }
    }
}

return __NAMESPACE__;