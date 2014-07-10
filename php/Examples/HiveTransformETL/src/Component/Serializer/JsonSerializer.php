<?php
namespace Examples\HiveTransformETL\Component\Serializer;

use \Examples\HiveTransformETL\Encoder\JsonEncoder;

/**
 * JSON serializer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class JsonSerializer implements ISerializer {
    /**
     * 
     * @var \Examples\HiveTransformETL\Encoder\JsonEncoder
     */
    protected $encoder;
    
    /**
     * Constructor. Ensure that encoder is set.
     */
    public function __construct() {
        $this->encoder = JsonEncoder::instance();
    }
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Component\Serializer\JsonSerializer
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Serializer\ISerializer::serialize()
     */
    public function serialize($input) {
        return $this->encoder->encode($input);
    }
}