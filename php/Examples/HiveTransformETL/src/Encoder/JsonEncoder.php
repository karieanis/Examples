<?php
namespace Examples\HiveTransformETL\Encoder;

/**
 * OO wrapper class for json encoding with error handling
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class JsonEncoder implements IEncoder {
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
     * @return \Examples\HiveTransformETL\Encoder\JsonEncoder
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Encoder\IEncoder::encode()
     */
    public function encode($value, $options = 0) {
        $encoded = json_encode($value, $options);
        
        if(($errCode = json_last_error()) !== JSON_ERROR_NONE) { 
            throw new EncodeException(static::$errors[$errCode], $errCode);
        }
        
        return $encoded;
    }
}