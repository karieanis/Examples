<?php
namespace Examples\HiveTransformETL\Component\Deserializer;

use Examples\HiveTransformETL\Decoder\TokenizedDecoder;

/**
 * A deserialization implementation utilising tokens
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class TokenizedDeserializer implements IDeserializer {
    /**
     * 
     * @var string
     */
    protected $token;
    /**
     * 
     * @var TokenizedDecoder
     */
    protected $decoder;
    
    public function __construct() {
        $this->decoder = TokenizedDecoder::instance();
    }
    
    /**
     * 
     * @return \Examples\HiveTransformETL\Component\Deserializer\TokenizedDeserializer
     */
    public static function instance() {
        return new static;
    }

    /**
     * 
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
    
    /**
     * 
     * @param string $token
     * @return \Examples\HiveTransformETL\Component\Deserializer\TokenizedDeserializer
     */
    public function setToken($token) {
        $this->token = $token;
        return $this;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Deserializer\IDeserializer::deserialize()
     */
    public function deserialize($input) {
        return $this->decoder->decode($input, $this->getToken());;
    }
}