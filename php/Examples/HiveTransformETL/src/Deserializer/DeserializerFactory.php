<?php
namespace Examples\HiveTransformETL\Deserializer;

/**
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class DeserializerFactory {
    /**
     * 
     * @param string $className
     * @return \Examples\HiveTransformETL\Component\Deserializer\IDeserializer
     */
    public static function factory($className) {
        return new $className;
    }
}