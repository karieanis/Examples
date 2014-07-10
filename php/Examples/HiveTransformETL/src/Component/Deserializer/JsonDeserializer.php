<?php
namespace Examples\HiveTransformETL\Component\Deserializer;

use \Examples\HiveTransformETL\Decoder\JsonDecoder;

/**
 * JSON deserializer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class JsonDeserializer implements IDeserializer {
    /**
     * 
     * @var \Examples\HiveTransformETL\Decoder\JsonDecoder
     */
    protected $decoder;
    
    /**
     * Constructor. Ensure that decoder is set.
     */
    public function __construct() {
        $this->decoder = JsonDecoder::instance();
    }
    
    /**
     * Get an instance
     * @return \Examples\HiveTransformETL\Component\Deserializer\JsonDeserializer
     */
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Deserializer\IDeserializer::deserialize()
     */
    public function deserialize($input) {
        return $this->decoder->decode($input, true);
    }
}