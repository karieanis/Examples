<?php
namespace Examples\HiveTransformETL\Component\Serializer;

/**
 * A serialization implementation utilising tokens
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class TokenizedSerializer implements ISerializer {
    /**
     * 
     * @var string
     */
    protected $token;

    /**
     * 
     * @return \Examples\HiveTransformETL\Component\Serializer\TokenizedSerializer
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
     * @return \Examples\HiveTransformETL\Component\Serializer\TokenizedSerializer
     */
    public function setToken($token) {
        $this->token = $token;
        return $this;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Serializer\ISerializer::serialize()
     */
    public function serialize($input) {
        return implode($this->getToken(), $input);
    }
}