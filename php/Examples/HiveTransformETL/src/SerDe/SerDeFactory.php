<?php
namespace Examples\HiveTransformETL\SerDe;

final class SerDeFactory {
    /**
     * 
     * @param string $className
     * @return \Examples\HiveTransformETL\Component\SerDe\SerDe
     */
    public static function factory($className) {
        return new $className;
    }
}