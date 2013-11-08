<?php
namespace Examples\HiveTransformETL\Component\Deserializer;

/**
 * JSON deserializer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class JsonDeserializer implements IDeserializer {
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Deserializer\IDeserializer::deserialize()
     */
    public function deserialize($input) {
        return json_decode($input, true);
    }
}