<?php
namespace Examples\HiveTransformETL\Cache;

/**
 * Cache factory class
 * @final
 * @author Jeremy Rayner <jeremy@davros.com.au>
 *
 */
final class CacheFactory {
    /**
     * Manufacture a new cache instance
     * @param string $cacheClass            The cache class to manufacture
     * @return ICache                       A cache instance
     */
    public static function factory($cacheClass) {
        return new $cacheClass;
    }
}