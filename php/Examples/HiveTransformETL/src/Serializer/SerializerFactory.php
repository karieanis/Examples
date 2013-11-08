<?php
namespace Examples\HiveTransformETL\Serializer;

/**
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class SerializerFactory {
    /**
     * 
     * @param string $className
     * @return \Examples\HiveTransformETL\Component\Serializer\ISerializer
     */
    public static function factory($className) {
        return new $className;
    }
}