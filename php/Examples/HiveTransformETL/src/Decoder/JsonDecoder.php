<?php
namespace Examples\HiveTransformETL\Decoder;

/**
 * OO wrapper class for json decoding with error handling
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class JsonDecoder implements IDecoder {
    /**
     * @staticvar array
     */
    protected static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Decoder\JsonDecoder
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Decoder\IDecoder::decode()
     */
    public function decode($value, $assoc = null) {
        $decoded = json_decode($value, $assoc);

        if(($errCode = json_last_error()) !== JSON_ERROR_NONE) {
            throw new DecodeException(static::$errors[$errCode], $errCode);
        }
        
        return $decoded;
    }
}