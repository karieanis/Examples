<?php
namespace Examples\HiveTransformETL\Test\Unit\Encoder;

use \Examples\HiveTransformETL\Encoder\JsonEncoder;

class JsonEncoderTest extends \PHPUnit_Framework_TestCase {
    public function testEncoderReturnsEncodedValue() {
        $this->assertEquals(
                "{\"test\":\"value\"}",
                JsonEncoder::instance()->encode(
                        array("test" => "value")
                )
        );
    }
    
    /**
     * @expectedException         \Examples\HiveTransformETL\Encoder\EncodeException
     * @expectedExceptionCode     JSON_ERROR_UTF8
     * @expectedExceptionMessage  Malformed UTF-8 characters, possibly incorrectly encoded
     */
    public function testEncoderThrowsException() {
        JsonEncoder::instance()->encode(
            array(
                "test" => "A strange string to pass, maybe with some ¿, ¾, Œ characters."
            )
        );
    }
}

return __NAMESPACE__;