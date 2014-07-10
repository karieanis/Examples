<?php
namespace Examples\HiveTransformETL\Decoder;

final class TokenizedDecoder implements IDecoder {
    public static function instance() {
        return new static;
    }
    
    public function decode($value, $token = null) {
        return explode($token, $value);
    }
}