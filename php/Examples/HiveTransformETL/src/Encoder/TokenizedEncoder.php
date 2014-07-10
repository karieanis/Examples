<?php
namespace Examples\HiveTransformETL\Encoder;

final class TokenizedEncoder implements IEncoder {
    public static function instance() {
        return new static;
    }
    
    public function encode($value, $token = null) {
        return implode($token, $value);
    }
}