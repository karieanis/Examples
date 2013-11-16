<?php
namespace Examples\HiveTransformETL\Test\Unit\Decoder;

use \Examples\HiveTransformETL\Decoder\JsonDecoder;

class JsonDecoderTest extends \PHPUnit_Framework_TestCase {
    public function testDecoderReturnsDecodedValue() {
        $this->assertEquals(
                array("test" => "value"),
                JsonDecoder::instance()->decode(
                        "{\"test\":\"value\"}",
                        true
                )
        );
    }
    
    /**
     * @expectedException           \Examples\HiveTransformETL\Decoder\DecodeException
     * @expectedExceptionCode       JSON_ERROR_SYNTAX
     * @expectedExceptionMessage    Syntax error, malformed JSON
     */
    public function testDecoderThrowsException() {
        JsonDecoder::instance()->decode(
                "{[", 
                true
        );
    }
}

return __NAMESPACE__;