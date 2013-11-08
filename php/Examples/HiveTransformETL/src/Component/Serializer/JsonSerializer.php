<?php
namespace Examples\HiveTransformETL\Component\Serializer;

/**
 * JSON serializer
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
class JsonSerializer implements ISerializer {
    public static function instance() {
        return new static;
    }
    
    /* (non-PHPdoc)
     * @see \Examples\HiveTransformETL\Component\Serializer\ISerializer::serialize()
     */
    public function serialize($input) {
        return json_encode($input);
    }
}